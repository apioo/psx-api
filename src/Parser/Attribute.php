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
use PSX\Api\Parser\Attribute\Meta;
use PSX\Api\ParserInterface;
use PSX\Api\Resource;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;
use PSX\Schema\Type\NumberType;
use PSX\Schema\Type\ScalarType;
use PSX\Schema\Type\StringType;
use PSX\Schema\Type\StructType;
use PSX\Schema\Type\TypeAbstract;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;
use ReflectionClass;
use RuntimeException;

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
     * @inheritdoc
     * @throws \PSX\Schema\Exception\InvalidSchemaException
     * @throws \PSX\Api\Exception\InvalidMethodException
     * @throws \ReflectionException
     */
    public function parse(string $schema, ?string $path = null): SpecificationInterface
    {
        if (!is_string($schema)) {
            throw new RuntimeException('Schema must be a class name');
        }

        $controller = new ReflectionClass($schema);
        $basePath   = dirname($controller->getFileName());

        $rootMeta = Meta::fromAttributes($controller->getAttributes());
        $specification = new Specification();

        $this->parseMethods($controller, $specification, $basePath, $rootMeta, $path);

        return $specification;
    }

    /**
     * @throws \PSX\Schema\Exception\InvalidSchemaException
     * @throws \PSX\Api\Exception\InvalidMethodException
     */
    private function parseMethods(ReflectionClass $controller, SpecificationInterface $specification, string $basePath, Meta $rootMeta, ?string $path)
    {
        foreach ($controller->getMethods() as $method) {
            $meta = Meta::fromAttributes($method->getAttributes());
            $meta->merge($rootMeta);

            if ($meta->isExcluded()) {
                continue;
            }

            if ($meta->hasPath()) {
                $path = $meta->getPath()->path;
            }

            if (empty($path)) {
                continue;
            }

            $typePrefix = $path;

            if ($specification->getResourceCollection()->has($path)) {
                $resource = $specification->getResourceCollection()->get($path);

                $pathType = $this->getParamType($meta->getPathParams());
                if ($pathType instanceof StructType) {
                    $typeName = $typePrefix . 'Path';
                    $specification->getDefinitions()->addType($typeName, $pathType);
                    $resource->setPathParameters($typeName);
                }
            } else {
                $specification->getResourceCollection()->set($resource = new Resource(Resource::STATUS_ACTIVE, $path));
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

            $resource->addMethod($this->parseMethod(
                $httpMethod,
                $meta,
                $specification->getDefinitions(),
                $typePrefix,
                $basePath
            ));
        }
    }

    /**
     * @throws \PSX\Api\Exception\InvalidMethodException
     * @throws \PSX\Schema\Exception\InvalidSchemaException
     */
    private function parseMethod(string $httpMethod, Meta $meta, DefinitionsInterface $definitions, string $typePrefix, string $basePath): Resource\MethodAbstract
    {
        $typePrefix = $typePrefix . $httpMethod;
        $method = Resource\Factory::getMethod($httpMethod);

        if ($meta->getOperationId() instanceof Attr\OperationId) {
            $method->setOperationId($meta->getOperationId()->operationId);
        }

        if ($meta->getDescription() instanceof Attr\Description) {
            $method->setDescription($meta->getDescription()->description);
        }

        $queryType = $this->getParamType($meta->getQueryParams());
        if ($queryType instanceof StructType) {
            $typeName = $typePrefix . 'Query';
            $definitions->addType($typeName, $queryType);
            $method->setQueryParameters($typeName);
        }

        if ($meta->getIncoming() instanceof Attr\Incoming) {
            $schema = $this->getBodySchema($meta->getIncoming(), $definitions, $basePath, $typePrefix . 'Request');
            if (!empty($schema)) {
                $method->setRequest($schema);
            }
        }

        foreach ($meta->getOutgoing() as $outgoing) {
            $schema = $this->getBodySchema($outgoing, $definitions, $basePath, $typePrefix . $outgoing->code . 'Response');
            if (!empty($schema)) {
                $method->addResponse($outgoing->code, $schema);
            }
        }

        if ($meta->getTags() instanceof Attr\Tags) {
            $method->setTags($meta->getTags()->tags);
        }

        if ($meta->getSecurity() instanceof Attr\Security) {
            $method->setSecurity($meta->getSecurity()->name, $meta->getSecurity()->scopes);
        }

        return $method;
    }

    /**
     * @throws \PSX\Schema\Exception\InvalidSchemaException
     */
    private function getBodySchema(Attr\SchemaAbstract $annotation, DefinitionsInterface $definitions, string $basePath, string $typeName): string
    {
        $schema = $annotation->schema;
        $type   = $annotation->type;

        // if we have a file append base path
        if (strpos($schema, '.') !== false) {
            $type   = SchemaManager::TYPE_TYPESCHEMA;
            $schema = $basePath . '/' . $schema;
        }

        $schema = $this->schemaManager->getSchema($schema, $type);

        $definitions->addSchema($typeName, $schema);

        return $typeName;
    }

    /**
     * @throws \PSX\Schema\Exception\InvalidSchemaException
     */
    private function getParamType(array $params): ?StructType
    {
        if (empty($params)) {
            return null;
        }

        $required = [];
        $struct = TypeFactory::getStruct();
        foreach ($params as $attribute) {
            if (!$attribute instanceof Attr\ParamAbstract) {
                continue;
            }

            if ($attribute->required) {
                $required[] = $attribute->name;
            }

            $struct->addProperty($attribute->name, $this->getParameter($attribute));
        }

        if (!empty($required)) {
            $struct->setRequired($required);
        }

        return $struct;
    }

    /**
     * @throws \PSX\Schema\Exception\InvalidSchemaException
     */
    private function getParameter(Attr\ParamAbstract $param): TypeInterface
    {
        switch ($param->type) {
            case 'integer':
                $type = TypeFactory::getInteger();
                break;

            case 'number':
                $type = TypeFactory::getNumber();
                break;

            case 'boolean':
                $type = TypeFactory::getBoolean();
                break;

            case 'string':
            default:
                $type = TypeFactory::getString();
                break;
        }

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
}
