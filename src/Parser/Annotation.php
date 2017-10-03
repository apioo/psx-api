<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Schema\Property;
use PSX\Schema\SchemaInterface;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;
use ReflectionClass;
use RuntimeException;

/**
 * Annotation
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Annotation implements ParserInterface
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $annotationReader;

    /**
     * @var \PSX\Schema\SchemaManagerInterface
     */
    private $schemaManager;

    /**
     * @param \Doctrine\Common\Annotations\Reader $annotationReader
     * @param \PSX\Schema\SchemaManagerInterface $schemaManager
     */
    public function __construct(Reader $annotationReader, SchemaManagerInterface $schemaManager)
    {
        $this->annotationReader = $annotationReader;
        $this->schemaManager    = $schemaManager;
    }

    /**
     * @inheritdoc
     */
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
                $resource->setDescription($this->getDescription($annotation, $basePath));
            } elseif ($annotation instanceof Anno\PathParam) {
                $required[] = $annotation->getName();

                $resource->addPathParameter($annotation->getName(), $this->getParameter($annotation));
            }
        }

        $resource->getPathParameters()->setRequired($required);

        $this->parseMethods($controller, $resource, $basePath);

        return $resource;
    }

    /**
     * @param \ReflectionClass $controller
     * @param \PSX\Api\Resource $resource
     * @param string $basePath
     */
    private function parseMethods(ReflectionClass $controller, Resource $resource, $basePath)
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

            $method->setOperationId($reflection->getName());

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Anno\Description) {
                    $method->setDescription($this->getDescription($annotation, $basePath));
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

    private function getBodySchema(Anno\SchemaAbstract $annotation, $basePath)
    {
        $schema = $annotation->getSchema();
        $type   = $annotation->getType();

        // if we have a file append base path
        if (strpos($schema, '.') !== false) {
            $type   = SchemaManager::TYPE_JSONSCHEMA;
            $schema = $basePath . '/' . $schema;
        }

        return $this->schemaManager->getSchema($schema, $type);
    }

    private function getDescription(Anno\Description $annotation, $basePath)
    {
        $description = $annotation->getDescription();
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

    private function getParameter(Anno\ParamAbstract $param)
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
