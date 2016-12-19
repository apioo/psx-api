<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

use Doctrine\Common\Annotations\Reader;
use PSX\Api\Annotation as Anno;
use PSX\Api\ParserInterface;
use PSX\Api\Resource;
use PSX\Schema\Parser\JsonSchema;
use PSX\Schema\Property;
use PSX\Schema\SchemaInterface;
use PSX\Schema\SchemaManagerInterface;
use ReflectionClass;
use RuntimeException;

/**
 * Annotation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Annotation implements ParserInterface
{
    protected $annotationReader;
    protected $schemaManager;
    protected $resources;

    public function __construct(Reader $annotationReader, SchemaManagerInterface $schemaManager)
    {
        $this->annotationReader = $annotationReader;
        $this->schemaManager    = $schemaManager;
    }

    public function parse($schema, $path)
    {
        if (!is_string($schema)) {
            throw new RuntimeException('Schema must be a class name');
        }

        $resource    = new Resource(Resource::STATUS_ACTIVE, $path);
        $controller  = new ReflectionClass($schema);
        $basePath    = dirname($controller->getFileName());
        $required    = [];
        $annotations = $this->annotationReader->getClassAnnotations($controller);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Anno\Title) {
                $resource->setTitle($annotation->getTitle());
            } elseif ($annotation instanceof Anno\Description) {
                $resource->setDescription($annotation->getDescription());
            } elseif ($annotation instanceof Anno\PathParam) {
                $required[] = $annotation->getName();

                $resource->addPathParameter($annotation->getName(), $this->getParameter($annotation));
            }
        }

        $resource->getPathParameters()->setRequired($required);

        $this->parseMethods($controller, $resource, $basePath);

        return $resource;
    }

    protected function parseMethods(ReflectionClass $controller, Resource $resource, $basePath)
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

            $method      = Resource\Factory::getMethod($httpMethod);
            $reflection  = $controller->getMethod($methodName);
            $required    = [];
            $annotations = $this->annotationReader->getMethodAnnotations($reflection);

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Anno\Description) {
                    $method->setDescription($annotation->getDescription());
                } elseif ($annotation instanceof Anno\QueryParam) {
                    if ($annotation->isRequired()) {
                        $required[] = $annotation->getName();
                    }

                    $method->addQueryParameter($annotation->getName(), $this->getParameter($annotation));
                } elseif ($annotation instanceof Anno\Incoming) {
                    $schema = $this->getBodySchema($annotation, $basePath);
                    if ($schema instanceof SchemaInterface) {
                        $method->setRequest($schema);
                    }
                } elseif ($annotation instanceof Anno\Outgoing) {
                    $schema = $this->getBodySchema($annotation, $basePath);
                    if ($schema instanceof SchemaInterface) {
                        $method->addResponse($annotation->getCode(), $schema);
                    }
                } elseif ($annotation instanceof Anno\Exclude) {
                    // skip this method
                    continue 2;
                }
            }

            $method->getQueryParameters()->setRequired($required);

            $resource->addMethod($method);
        }
    }

    protected function getBodySchema(Anno\SchemaAbstract $annotation, $basePath)
    {
        $schema = $annotation->getSchema();

        // if we have a file append base path
        if (strpos($schema, '.') !== false) {
            $schema = $basePath . '/' . $schema;
        }

        return $this->schemaManager->getSchema($schema);
    }

    protected function getParameter(Anno\ParamAbstract $param)
    {
        switch ($param->getType()) {
            case 'integer':
                $property = Property::getInteger();
                break;

            case 'number':
                $property = Property::getNumber();
                break;

            case 'boolean':
                $property = Property::getBoolean();
                break;

            case 'null':
                $property = Property::getNull();
                break;

            case 'string':
            default:
                $property = Property::getString();
                break;
        }

        $description = $param->getDescription();
        if ($description !== null) {
            $property->setDescription($description);
        }

        $enum = $param->getEnum();
        if ($enum !== null && is_array($enum)) {
            $property->setEnum($enum);
        }

        $minLength = $param->getMinLength();
        if ($minLength !== null) {
            $property->setMinLength($minLength);
        }

        $maxLength = $param->getMaxLength();
        if ($maxLength !== null) {
            $property->setMaxLength($maxLength);
        }

        $pattern = $param->getPattern();
        if ($pattern !== null) {
            $property->setPattern($pattern);
        }

        $format = $param->getFormat();
        if ($format !== null) {
            $property->setFormat($format);
        }

        $minimum = $param->getMinimum();
        if ($minimum !== null) {
            $property->setMinimum($minimum);
        }

        $maximum = $param->getMaximum();
        if ($maximum !== null) {
            $property->setMaximum($maximum);
        }

        $multipleOf = $param->getMultipleOf();
        if ($multipleOf !== null) {
            $property->setMultipleOf($multipleOf);
        }

        return $property;
    }
}
