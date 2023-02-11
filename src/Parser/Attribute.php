<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\Exception\ParserException;
use PSX\Api\Operation;
use PSX\Api\Parser\Attribute\Meta;
use PSX\Api\ParserInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Exception\InvalidSchemaException;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;
use PSX\Schema\Type\NumberType;
use PSX\Schema\Type\ScalarType;
use PSX\Schema\Type\StringType;
use PSX\Schema\Type\TypeAbstract;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;
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

    private const METHOD_MAPPING = [
        'doGet' => 'GET',
        'doPost' => 'POST',
        'doPut' => 'PUT',
        'doDelete' => 'DELETE',
        'doPatch' => 'PATCH',
    ];

    public function __construct(SchemaManagerInterface $schemaManager)
    {
        $this->schemaManager = $schemaManager;
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

            $path = null;
            if ($meta->hasPath()) {
                $path = $meta->getPath()->path;
            }

            if (empty($path)) {
                continue;
            }

            $typePrefix = str_replace('\\', '_', $controller->getName()) . '_' . $method->getName();

            $operationId = $this->buildOperationId($controller->getName(), $method->getName());
            if ($specification->getOperations()->has($operationId)) {
                continue;
            }

            if (!$meta->hasMethod()) {
                // legacy way to detect the http method based on the method name
                $httpMethod = self::METHOD_MAPPING[$method->getName()] ?? null;
            } else {
                $httpMethod = $meta->getMethod()->method;
            }

            if (empty($httpMethod)) {
                continue;
            }

            // if we have no incoming attribute we parse it from the type hint of the first parameter
            if (!$meta->hasIncoming()) {
                // @TODO use all arguments
                /*
                $firstParameter = $method->getParameters()[0] ?? null;
                if ($firstParameter instanceof \ReflectionParameter) {
                    $schema = $this->getSchemaFromTypeHint($firstParameter->getType());
                    if (!empty($schema) && class_exists($schema)) {
                        $meta->setIncoming(new Attr\Incoming($schema));
                    }
                }
                */
            }

            // if we have no outgoing attribute we parse it from the return type hint
            if (!$meta->hasOutgoing()) {
                $schema = $this->getSchemaFromTypeHint($method->getReturnType());
                if (!empty($schema) && class_exists($schema)) {
                    $meta->addOutgoing(new Attr\Outgoing(200, $schema));
                }
            }

            $return = $this->getReturn($meta, $specification->getDefinitions(), $basePath, $typePrefix);
            if (!$return instanceof Operation\Response) {
                throw new ParserException('Method ' . $controller->getName() . '::' . $method->getName() . ' has not defined a successful response');
            }

            $operation = new Operation($httpMethod, $path, $return);
            $operation->setArguments($this->getArguments($meta, $specification->getDefinitions(), $basePath, $typePrefix));

            $throws = $this->getThrows($meta, $specification->getDefinitions(), $basePath, $typePrefix);
            if (count($throws) > 0) {
                $operation->setThrows($throws);
            }

            if ($meta->getDescription() instanceof Attr\Description) {
                $operation->setDescription($this->resolveDescription($meta->getDescription()));
            }

            if ($meta->getDeprecated() instanceof Attr\Deprecated) {
                $operation->setDeprecated($meta->getDeprecated()->deprecated);
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
    private function getArguments(Meta $meta, DefinitionsInterface $definitions, string $basePath, string $typePrefix): Operation\Arguments
    {
        $arguments = new Operation\Arguments();

        foreach ($meta->getPathParams() as $attribute) {
            if (!$attribute instanceof Attr\ParamAbstract) {
                continue;
            }

            $arguments->add($attribute->name, new Operation\Argument('path', $this->getParameter($attribute)));
        }

        foreach ($meta->getQueryParams() as $attribute) {
            if (!$attribute instanceof Attr\ParamAbstract) {
                continue;
            }

            $arguments->add($attribute->name, new Operation\Argument('query', $this->getParameter($attribute)));
        }

        if ($meta->getIncoming() instanceof Attr\Incoming) {
            $schema = $this->getBodySchema($meta->getIncoming(), $definitions, $basePath, $typePrefix . '_Request');

            $arguments->add('payload', new Operation\Argument('body', $schema));
        }

        return $arguments;
    }

    /**
     * @throws InvalidSchemaException
     */
    private function getReturn(Meta $meta, DefinitionsInterface $definitions, string $basePath, string $typePrefix): ?Operation\Response
    {
        $responses = $this->getResponsesInRange($meta, 200, 300);
        $response = $responses[0] ?? null;

        if (!$response instanceof Attr\Outgoing) {
            return null;
        }

        $schema = $this->getBodySchema($response, $definitions, $basePath, $typePrefix . '_' . $response->code . '_Response');

        return new Operation\Response($response->code, $schema);
    }

    /**
     * @return Operation\Response[]
     * @throws InvalidSchemaException
     */
    private function getThrows(Meta $meta, DefinitionsInterface $definitions, string $basePath, string $typePrefix): array
    {
        $throws = [];

        $responses = $this->getResponsesInRange($meta, 400, 600);
        foreach ($responses as $response) {
            $schema = $this->getBodySchema($response, $definitions, $basePath, $typePrefix . '_' . $response->code . '_Response');

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
    private function getBodySchema(Attr\SchemaAbstract $annotation, DefinitionsInterface $definitions, string $basePath, string $typeName): TypeInterface
    {
        $schema = $annotation->schema;
        $type   = $annotation->type;

        // if we have a file append base path
        if (str_contains($schema, '.')) {
            $type = SchemaManager::TYPE_TYPESCHEMA;
            if (!is_file($schema)) {
                $schema = $basePath . '/' . $schema;
            }
        }

        $schema = $this->schemaManager->getSchema($schema, $type);

        $definitions->addSchema($typeName, $schema);

        return $schema->getType();
    }

    /**
     * @throws InvalidSchemaException
     */
    private function getParameter(Attr\ParamAbstract $param): TypeInterface
    {
        $type = match ($param->type) {
            TypeAbstract::TYPE_INTEGER => TypeFactory::getInteger(),
            TypeAbstract::TYPE_NUMBER => TypeFactory::getNumber(),
            TypeAbstract::TYPE_BOOLEAN => TypeFactory::getBoolean(),
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
        if ($type instanceof \ReflectionNamedType && class_exists($type->getName())) {
            return $type->getName();
        } elseif ($type instanceof \ReflectionUnionType) {
            // @TODO
            return null;
        }

        return null;
    }

    /*
    private function getArgumentAttributeForProperty(\ReflectionParameter $parameter): ?Attr\Argument
    {
        $attributes = $parameter->getAttributes();
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof Attr\Argument) {
                return $instance;
            }
        }

        return null;
    }

    private function getTypeFromType(\ReflectionType $type, DefinitionsInterface $definitions, string &$in): ?TypeInterface
    {
        if (!$type instanceof \ReflectionNamedType) {
            return null;
        }

        $return = match ($type->getName()) {
            'string' => TypeFactory::getString(),
            'int' => TypeFactory::getInteger(),
            'float' => TypeFactory::getNumber(),
            'bool' => TypeFactory::getBoolean(),
            'mixed' => TypeFactory::getAny(),
            'resource' => TypeFactory::getBinary(),
            DateTime::class, \DateTimeInterface::class, \DateTimeImmutable::class, \DateTime::class => TypeFactory::getDateTime(),
            Date::class => TypeFactory::getDate(),
            Time::class => TypeFactory::getTime(),
            Duration::class, \DateInterval::class => TypeFactory::getDuration(),
            Uri::class => TypeFactory::getUri(),
            default => null,
        };

        if ($return === null) {
            if (enum_exists($type->getName())) {
                $type = $this->getTypeFromEnum(new \ReflectionEnum($type->getName()), $definitions);
            } elseif (class_exists($type->getName())) {
                $in = 'body';
                $type = $this->getTypeFromClass(new ReflectionClass($type->getName()), $definitions);
            }
        }

        if ($return !== null) {
            $return->setNullable($type->allowsNull());
        }

        return $return;
    }

    private function getTypeFromEnum(\ReflectionEnum $enum, DefinitionsInterface $definitions): TypeInterface
    {
        if ($enum->isBacked()) {
            $type = $this->getTypeFromType($enum->getBackingType(), $definitions, $in);
        } else {
            $type = TypeFactory::getString();
        }

        $values = [];
        $cases = $enum->getCases();
        foreach ($cases as $case) {
            if ($case instanceof \ReflectionEnumBackedCase) {
                $values[] = $case->getBackingValue();
            } elseif ($case instanceof \ReflectionEnumUnitCase) {
                $values[] = $case->getName();
            }
        }

        return $type->setEnum($values);
    }
    */

    private function buildOperationId(string $controllerName, string $methodName): string
    {
        return str_replace('\\', '.', $controllerName . '.' . $methodName);
    }
}
