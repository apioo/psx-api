<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Api\Parser;

use PSX\Api\Attribute as Attr;
use PSX\Api\Attribute\ParamAbstract;
use PSX\Api\Exception\ParserException;
use PSX\Api\Model\Passthru;
use PSX\Api\Operation;
use PSX\Api\Parser\Attribute\Meta;
use PSX\Api\ParserInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Api\Util\Inflection;
use PSX\DateTime\Duration;
use PSX\DateTime\LocalDate;
use PSX\DateTime\LocalDateTime;
use PSX\DateTime\LocalTime;
use PSX\DateTime\Period;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Exception\InvalidSchemaException;
use PSX\Schema\Format;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;
use PSX\Schema\Type\NumberType;
use PSX\Schema\Type\ScalarType;
use PSX\Schema\Type\StringType;
use PSX\Schema\Type;
use PSX\Schema\Type\TypeAbstract;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;
use PSX\Uri\Uri;
use ReflectionClass;

/**
 * Attribute
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Attribute implements ParserInterface
{
    private SchemaManagerInterface $schemaManager;
    private bool $inspectTypeHints;

    public function __construct(SchemaManagerInterface $schemaManager, bool $inspectTypeHints = true)
    {
        $this->schemaManager = $schemaManager;
        $this->inspectTypeHints = $inspectTypeHints;
    }

    /**
     * @throws ParserException
     */
    public function parse(string $schema): SpecificationInterface
    {
        try {
            $controller = new ReflectionClass($schema);
            $basePath   = dirname($controller->getFileName());

            $rootMeta = Meta::fromAttributes($controller->getAttributes());
            $specification = new Specification();

            $this->parseMethods($controller, $specification, $basePath, $rootMeta);

            return $specification;
        } catch (\ReflectionException $e) {
            throw new ParserException('Provided schema must be a valid class', 0, $e);
        } catch (\Throwable $e) {
            throw new ParserException('An error occurred while parsing: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws ParserException
     * @throws InvalidSchemaException
     */
    private function parseMethods(ReflectionClass $controller, SpecificationInterface $specification, string $basePath, Meta $rootMeta)
    {
        foreach ($controller->getMethods() as $method) {
            $meta = Meta::fromAttributes($method->getAttributes());
            $meta->merge($rootMeta);

            if ($meta->isExcluded()) {
                continue;
            }

            $path = $meta->getPath()?->path;
            if (empty($path)) {
                continue;
            }

            $operationId = self::buildOperationId($controller->getName(), $method->getName());
            if ($specification->getOperations()->has($operationId)) {
                continue;
            }

            $httpMethod = $meta->getMethod()?->method;
            if (empty($httpMethod)) {
                continue;
            }

            if ($this->inspectTypeHints) {
                $this->inspectTypeHints($method, $meta);
            }

            $return = $this->getReturn($meta, $specification->getDefinitions(), $basePath);
            if (!$return instanceof Operation\Response) {
                throw new ParserException('Method ' . $controller->getName() . '::' . $method->getName() . ' has not defined a successful response');
            }

            $operation = new Operation($httpMethod, $path, $return);
            $operation->setArguments($this->getArguments($meta, $specification->getDefinitions(), $basePath));

            $throws = $this->getThrows($meta, $specification->getDefinitions(), $basePath);
            if (count($throws) > 0) {
                $operation->setThrows($throws);
            }

            if ($meta->getDescription() instanceof Attr\Description) {
                $operation->setDescription($this->resolveDescription($meta->getDescription()));
            }

            if ($meta->getDeprecated() instanceof Attr\Deprecated) {
                $operation->setStability($meta->getDeprecated()->deprecated);
            }

            if ($meta->getSecurity() instanceof Attr\Security) {
                $operation->setSecurity($meta->getSecurity()->scopes);
            }

            if ($meta->getAuthorization() instanceof Attr\Authorization) {
                $operation->setAuthorization($meta->getAuthorization()->authorization);
            }

            if ($meta->getTags() instanceof Attr\Tags) {
                $operation->setTags($meta->getTags()->tags);
            }

            $specification->getOperations()->add($operationId, $operation);
        }
    }

    /**
     * @throws InvalidSchemaException
     */
    private function getArguments(Meta $meta, DefinitionsInterface $definitions, string $basePath): Operation\Arguments
    {
        $arguments = new Operation\Arguments();

        foreach ($meta->getPathParams() as $attribute) {
            if (!$attribute instanceof Attr\ParamAbstract) {
                continue;
            }

            $arguments->add($attribute->name, new Operation\Argument('path', $this->getParameter($attribute)));
        }

        foreach ($meta->getHeaderParams() as $attribute) {
            if (!$attribute instanceof Attr\ParamAbstract) {
                continue;
            }

            $arguments->add($attribute->name, new Operation\Argument('header', $this->getParameter($attribute)));
        }

        foreach ($meta->getQueryParams() as $attribute) {
            if (!$attribute instanceof Attr\ParamAbstract) {
                continue;
            }

            $arguments->add($attribute->name, new Operation\Argument('query', $this->getParameter($attribute)));
        }

        if ($meta->getIncoming() instanceof Attr\Incoming) {
            $schema = $this->getBodySchema($meta->getIncoming(), $definitions, $basePath);

            $arguments->add($meta->getIncoming()->name ?? 'payload', new Operation\Argument('body', $schema));
        }

        return $arguments;
    }

    /**
     * @throws InvalidSchemaException
     */
    private function getReturn(Meta $meta, DefinitionsInterface $definitions, string $basePath): ?Operation\Response
    {
        $responses = $this->getResponsesInRange($meta, 200, 300);
        $response = $responses[0] ?? null;

        if (!$response instanceof Attr\Outgoing) {
            return null;
        }

        $schema = $this->getBodySchema($response, $definitions, $basePath);

        return new Operation\Response($response->code, $schema);
    }

    /**
     * @return Operation\Response[]
     * @throws InvalidSchemaException
     */
    private function getThrows(Meta $meta, DefinitionsInterface $definitions, string $basePath): array
    {
        $throws = [];

        $responses = $this->getResponsesInRange($meta, 400, 600);
        foreach ($responses as $response) {
            $schema = $this->getBodySchema($response, $definitions, $basePath);

            $throws[] = new Operation\Response($response->code, $schema);
        }

        return $throws;
    }

    /**
     * @return Attr\Outgoing[]
     */
    public function getResponsesInRange(Meta $meta, int $start, int $end): array
    {
        $result = [];
        foreach ($meta->getOutgoing() as $outgoing) {
            if ($outgoing->code >= $start && $outgoing->code < $end) {
                $result[] = $outgoing;
            }
        }
        return $result;
    }

    /**
     * @throws InvalidSchemaException
     */
    private function getBodySchema(Attr\SchemaAbstract $annotation, DefinitionsInterface $definitions, string $basePath): TypeInterface
    {
        $schema = $annotation->schema;
        $type   = $annotation->type;

        // if we have a file append base path
        if (str_contains($schema, '.')) {
            if (!is_file($schema)) {
                $schema = $basePath . '/' . $schema;
            }
        }

        $schema = $this->schemaManager->getSchema($schema, $type);

        $definitions->merge($schema->getDefinitions());

        return $schema->getType();
    }

    /**
     * @throws InvalidSchemaException
     */
    private function getParameter(Attr\ParamAbstract $param): TypeInterface
    {
        $type = match ($param->type) {
            Type::INTEGER => TypeFactory::getInteger(),
            Type::NUMBER => TypeFactory::getNumber(),
            Type::BOOLEAN => TypeFactory::getBoolean(),
            default => TypeFactory::getString(),
        };

        if ($type instanceof TypeAbstract) {
            $description = $param->description;
            if ($description !== null) {
                $type->setDescription($description);
            }
        }

        if ($type instanceof ScalarType) {
            $enum = $param->enum;
            if (is_array($enum)) {
                $type->setEnum($enum);
            }
        }

        if ($type instanceof StringType) {
            $minLength = $param->minLength;
            if ($minLength !== null) {
                $type->setMinLength($minLength);
            }

            $maxLength = $param->maxLength;
            if ($maxLength !== null) {
                $type->setMaxLength($maxLength);
            }

            $pattern = $param->pattern;
            if ($pattern !== null) {
                $type->setPattern($pattern);
            }

            $format = $param->format;
            if ($format !== null) {
                $type->setFormat($format);
            }
        } elseif ($type instanceof NumberType) {
            $minimum = $param->minimum;
            if ($minimum !== null) {
                $type->setMinimum($minimum);
            }

            $maximum = $param->maximum;
            if ($maximum !== null) {
                $type->setMaximum($maximum);
            }
        }

        return $type;
    }

    /**
     * @throws ParserException
     */
    private function resolveDescription(Attr\Description $attribute): string
    {
        $description = $attribute->description;

        if (str_starts_with($description, 'file://')) {
            $file = substr($description, 7);
            if (!is_file($file)) {
                throw new ParserException('Provided description file "' . $file . '" does not exist');
            }

            return file_get_contents($file);
        } else {
            return $description;
        }
    }

    private function getSchemaFromTypeHint(?\ReflectionType $type): ?string
    {
        if ($type instanceof \ReflectionNamedType) {
            if ($type->getName() === 'mixed') {
                return Passthru::class;
            } elseif (class_exists($type->getName())) {
                return $type->getName();
            }
        } elseif ($type instanceof \ReflectionUnionType) {
            // @TODO
            return null;
        }

        return null;
    }

    private function inspectTypeHints(\ReflectionMethod $method, Meta $meta): void
    {
        $missingPathNames = $this->getMissingPathNames($meta);
        $pathNames = $this->getParamNames($meta->getPathParams());
        $headerNames = $this->getParamNames($meta->getHeaderParams());
        $queryNames = $this->getParamNames($meta->getQueryParams());

        $pathParams = [];
        $headerParams = [];
        $queryParams = [];
        $incoming = null;
        foreach ($method->getParameters() as $parameter) {
            if (isset($pathNames[$parameter->getName()])) {
                $pathParams[] = $meta->getPathParams()[$pathNames[$parameter->getName()]];
            } elseif (isset($headerNames[$parameter->getName()])) {
                $headerParams[] = $meta->getHeaderParams()[$headerNames[$parameter->getName()]];
            } elseif (isset($queryNames[$parameter->getName()])) {
                $queryParams[] = $meta->getQueryParams()[$queryNames[$parameter->getName()]];
            } elseif (in_array($parameter->getName(), $missingPathNames)) {
                // in case the path contains a variable path fragment which is no yet mapped through a path param
                $args = $this->getParamArgsFromType($parameter, true);
                if (!empty($args)) {
                    $pathParams[] = new Attr\PathParam(...$args);
                }
            } else {
                // in all other cases the parameter is either a query parameter in case it is a scalar value or a body
                // parameter in case it is a class
                $args = $this->getParamArgsFromType($parameter, false);
                if (!empty($args)) {
                    $queryParams[] = new Attr\QueryParam(...$args);
                } elseif (!$meta->hasIncoming() && in_array($meta->getMethod()->method, ['POST', 'PUT', 'PATCH'])) {
                    $schema = $this->getSchemaFromTypeHint($parameter->getType());
                    if (!empty($schema)) {
                        if (!class_exists($schema)) {
                            throw new ParserException('The method ' . $method->getName() . ' contains an argument "' . $parameter->getName() . '" which has as type-hint a non existing class "' . $schema . '"');
                        }
                        if ($incoming !== null) {
                            throw new ParserException('The method ' . $method->getName() . ' must contains already the argument "' . $incoming->name . '" which represents the request body, we can not also set "' . $parameter->getName() . '" as request body');
                        }
                        $incoming = new Attr\Incoming($schema, SchemaManager::TYPE_ANNOTATION, $parameter->getName());
                    }
                }
            }
        }

        $meta->setPathParams($pathParams);
        $meta->setHeaderParams($headerParams);
        $meta->setQueryParams($queryParams);

        if ($incoming !== null) {
            $meta->setIncoming($incoming);
        }

        // if we have no outgoing attribute we parse it from the return type hint
        if (!$meta->hasOutgoing()) {
            $schema = $this->getSchemaFromTypeHint($method->getReturnType());
            if (!empty($schema) && class_exists($schema)) {
                $meta->addOutgoing(new Attr\Outgoing(200, $schema));
            }
        }
    }

    private function getMissingPathNames(Meta $meta): array
    {
        $pathNames = Inflection::extractPlaceholderNames($meta->getPath()->path);

        $availableNames = $this->getParamNames($meta->getPathParams());
        $missingNames = [];
        foreach ($pathNames as $pathName) {
            if (!isset($availableNames[$pathName])) {
                $missingNames[] = $pathName;
            }
        }

        return $missingNames;
    }

    /**
     * @param array<ParamAbstract> $params
     */
    private function getParamNames(array $params): array
    {
        $result = [];
        foreach ($params as $index => $param) {
            $result[$param->name] = $index;
        }
        return $result;
    }

    private function getParamArgsFromType(?\ReflectionParameter $parameter, bool $required, ?array $enum = null): ?array
    {
        $name = $parameter->getName();
        $type = $parameter->getType();
        if (!$type instanceof \ReflectionNamedType) {
            return null;
        }

        $return = match ($type->getName()) {
            'string' => [$name, Type::STRING, '', $required, $enum],
            'int' => [$name, Type::INTEGER, '', $required, $enum],
            'float' => [$name, Type::NUMBER, '', $required, $enum],
            'bool' => [$name, Type::BOOLEAN, '', $required, $enum],
            'mixed' => [$name, Type::ANY, '', $required, $enum],
            'resource' => [$name, Type::STRING, '', $required, $enum, null, null, null, Format::BINARY],
            LocalDateTime::class, \DateTimeInterface::class, \DateTimeImmutable::class, \DateTime::class => [$name, Type::STRING, '', $required, $enum, null, null, null, Format::DATETIME],
            LocalDate::class => [$name, Type::STRING, '', $required, $enum, null, null, null, Format::DATE],
            LocalTime::class => [$name, Type::STRING, '', $required, $enum, null, null, null, Format::TIME],
            Period::class, \DateInterval::class => [$name, Type::STRING, '', $required, $enum, null, null, null, Format::PERIOD],
            Duration::class => [$name, Type::STRING, '', $required, $enum, null, null, null, Format::DURATION],
            Uri::class => [$name, Type::STRING, '', $required, $enum, null, null, null, Format::URI],
            default => null,
        };

        /*
        if ($return === null && function_exists('enum_exists') && enum_exists($type->getName())) {
            $return = $this->getTypeFromEnum(new \ReflectionEnum($type->getName()), $required);
        }
        */

        return $return;
    }

    /*
    private function getTypeFromEnum(\ReflectionEnum $enum, bool $required): ?array
    {
        if (!$enum->isBacked()) {
            // we handle only backed enums since we have only in this case a stable value
            return null;
        }

        $values = [];
        $cases = $enum->getCases();
        foreach ($cases as $case) {
            if ($case instanceof \ReflectionEnumBackedCase) {
                $values[] = $case->getBackingValue();
            }
        }

        return $this->getParamArgsFromType($enum->getBackingType(), $required, $values);
    }
    */

    public static function buildOperationId(string $controllerName, string $methodName): string
    {
        return str_replace('\\', '.', $controllerName . '.' . $methodName);
    }
}
