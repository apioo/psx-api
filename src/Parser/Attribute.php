<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\ParserInterface;
use PSX\Api\Resource;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Definitions;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;
use PSX\Schema\Type\NumberType;
use PSX\Schema\Type\ScalarType;
use PSX\Schema\Type\StringType;
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
 * @link    http://phpsx.org
 */
class Attribute implements ParserInterface
{
    private SchemaManagerInterface $schemaManager;

    public function __construct(SchemaManagerInterface $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    /**
     * @inheritdoc
     */
    public function parse(string $schema, ?string $path = null): SpecificationInterface
    {
        if (!is_string($schema)) {
            throw new RuntimeException('Schema must be a class name');
        }

        $resource    = new Resource(Resource::STATUS_ACTIVE, $path);
        $definitions = new Definitions();

        $controller = new ReflectionClass($schema);
        $basePath   = dirname($controller->getFileName());
        $required   = [];
        $attributes = $controller->getAttributes();

        $path = TypeFactory::getStruct();
        foreach ($attributes as $attribute) {
            if ($attribute instanceof Attr\Title) {
                $resource->setTitle($attribute->title);
            } elseif ($attribute instanceof Attr\Description) {
                $resource->setDescription($this->getDescription($attribute, $basePath));
            } elseif ($attribute instanceof Attr\PathParam) {
                if ($attribute->required) {
                    $required[] = $attribute->name;
                }

                $path->addProperty($attribute->name, $this->getParameter($attribute));
            }
        }

        if ($path->getProperties()) {
            $typeName = 'Path';

            $path->setRequired($required);
            $definitions->addType($typeName, $path);
            $resource->setPathParameters($typeName);
        }

        $this->parseMethods($controller, $resource, $definitions, $basePath);

        return Specification::fromResource($resource, $definitions);
    }

    /**
     * @param \ReflectionClass $controller
     * @param \PSX\Api\Resource $resource
     * @param DefinitionsInterface $definitions
     * @param string $basePath
     */
    private function parseMethods(ReflectionClass $controller, Resource $resource, DefinitionsInterface $definitions, string $basePath)
    {
        $methods = [
            'GET'    => 'doGet',
            'POST'   => 'doPost',
            'PUT'    => 'doPut',
            'DELETE' => 'doDelete',
            'PATCH'  => 'doPatch'
        ];

        foreach ($methods as $httpMethod => $methodName) {
            // check whether method exists
            if (!$controller->hasMethod($methodName)) {
                continue;
            }

            $method     = Resource\Factory::getMethod($httpMethod);
            $reflection = $controller->getMethod($methodName);
            $required   = [];
            $attributes = $reflection->getAttributes();

            $method->setOperationId($reflection->getName());

            $query = TypeFactory::getStruct();
            $typePrefix = str_replace('\\', '', $controller->getName()) . ucfirst(strtolower($httpMethod));

            foreach ($attributes as $attribute) {
                if ($attribute instanceof Attr\Description) {
                    $method->setDescription($this->getDescription($attribute, $basePath));
                } elseif ($attribute instanceof Attr\QueryParam) {
                    if ($attribute->required) {
                        $required[] = $attribute->name;
                    }

                    $query->addProperty($attribute->name, $this->getParameter($attribute));
                } elseif ($attribute instanceof Attr\Incoming) {
                    $schema = $this->getBodySchema($attribute, $definitions, $basePath, $typePrefix . 'Request');
                    if (!empty($schema)) {
                        $method->setRequest($schema);
                    }
                } elseif ($attribute instanceof Attr\Outgoing) {
                    $schema = $this->getBodySchema($attribute, $definitions, $basePath, $typePrefix . $attribute->code . 'Response');
                    if (!empty($schema)) {
                        $method->addResponse($attribute->code, $schema);
                    }
                } elseif ($attribute instanceof Attr\Exclude) {
                    // skip this method
                    continue 2;
                }
            }

            if ($query->getProperties()) {
                $typeName = ucfirst(strtolower($methodName)) . 'Query';

                $query->setRequired($required);
                $definitions->addType($typeName, $query);
                $method->setQueryParameters($typeName);
            }

            $resource->addMethod($method);
        }
    }

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

    private function getDescription(Attr\Description $annotation, $basePath)
    {
        $description = $annotation->description;
        if (substr($description, 0, 8) === '!include') {
            $file = $basePath . '/' . trim(substr($description, 9));
            if (is_file($file)) {
                return file_get_contents($file);
            } else {
                throw new RuntimeException('Could not include file ' . $file);
            }
        } else {
            return $description;
        }
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
