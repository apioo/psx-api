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

use PSX\Api\ParserCollectionInterface;
use PSX\Api\ParserInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\Schema\Parser\JsonSchema;
use PSX\Schema\Property;
use PSX\Schema\PropertyInterface;
use PSX\Schema\Schema;
use PSX\Uri\Uri;
use RuntimeException;

/**
 * OpenAPI
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OpenAPI implements ParserInterface, ParserCollectionInterface
{
    /**
     * @var string|null
     */
    private $basePath;

    /**
     * @var \PSX\Schema\Parser\JsonSchema\Document
     */
    private $document;

    /**
     * @var \PSX\Schema\Parser\JsonSchema\RefResolver
     */
    private $resolver;

    /**
     * @var array
     */
    private $pathStack;

    /**
     * @var integer
     */
    private $stackIndex;

    /**
     * @var array
     */
    private $data;

    /**
     * @param string $basePath
     * @param \PSX\Schema\Parser\JsonSchema\RefResolver|null $resolver
     */
    public function __construct($basePath = null, JsonSchema\RefResolver $resolver = null)
    {
        $this->basePath = $basePath;
        $this->resolver = $resolver === null ? JsonSchema\RefResolver::createDefault() : $resolver;
    }

    /**
     * @inheritdoc
     */
    public function parse($schema, $path)
    {
        $this->setUp($schema);

        $paths = $this->getPaths();
        $path  = Inflection::transformRoutePlaceholder($path);

        if (isset($paths[$path])) {
            return $this->parseResource($paths[$path], $path);
        } else {
            throw new RuntimeException('Could not find resource definition "' . $path . '" in OpenAPI schema');
        }
    }

    /**
     * @inheritdoc
     */
    public function parseAll($schema)
    {
        $this->setUp($schema);

        $paths  = $this->getPaths();
        $result = new ResourceCollection();

        foreach ($paths as $path => $spec) {
            $resource = $this->parseResource($spec, Inflection::transformRoutePlaceholder($path));
            $result->set($resource);
        }

        return $result;
    }

    private function parseResource(array $data, $path)
    {
        $this->pushPath('paths');
        $this->pushPath($path);

        $status   = Resource::STATUS_ACTIVE;
        $resource = new Resource($status, $path);

        if (isset($data['summary'])) {
            $resource->setTitle($data['summary']);
        }

        if (isset($data['description'])) {
            $resource->setDescription($data['description']);
        }

        $this->parseUriParameters($resource, $data);

        foreach ($data as $methodName => $operation) {
            if (in_array($methodName, ['get', 'post', 'put', 'delete', 'patch']) && is_array($operation)) {
                $this->pushPath($methodName);

                $method = Resource\Factory::getMethod(strtoupper($methodName));

                if (isset($operation['operationId'])) {
                    $method->setOperationId($operation['operationId']);
                }

                if (isset($operation['summary'])) {
                    $method->setDescription($operation['summary']);
                }

                $this->parseQueryParameters($method, $operation);
                $this->parseRequest($method, $operation);
                $this->parseResponses($method, $operation);

                $resource->addMethod($method);

                $this->popPath();
            }
        }

        $this->popPath();
        $this->popPath();

        return $resource;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param array $data
     */
    private function parseUriParameters(Resource $resource, array $data)
    {
        list($properties, $required) = $this->parseParameters('path', $data);

        foreach ($properties as $name => $property) {
            $resource->addPathParameter($name, $property);
        }

        if (!empty($required)) {
            $resource->getPathParameters()->setRequired($required);
        }
    }

    /**
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @param array $data
     */
    private function parseQueryParameters(Resource\MethodAbstract $method, array $data)
    {
        list($properties, $required) = $this->parseParameters('query', $data);

        foreach ($properties as $name => $property) {
            $method->addQueryParameter($name, $property);
        }

        if (!empty($required)) {
            $method->getQueryParameters()->setRequired($required);
        }
    }

    /**
     * @param string $type
     * @param array $data
     * @return array
     */
    private function parseParameters($type, array $data)
    {
        $this->pushPath('parameters');

        $properties = [];
        $required   = [];

        if (isset($data['parameters']) && is_array($data['parameters'])) {
            foreach ($data['parameters'] as $index => $definition) {
                $this->pushPath($index);

                list($name, $property, $isRequired) = $this->parseParameter($type, $definition);

                if ($name !== null) {
                    if ($property !== null) {
                        $properties[$name] = $property;
                    }

                    if ($isRequired !== null && $isRequired === true) {
                        $required[] = $name;
                    }
                }

                $this->popPath();
            }
        }

        $this->popPath();

        return [
            $properties,
            $required,
        ];
    }

    private function parseParameter($type, array $data)
    {
        if (isset($data['$ref'])) {
            $ref  = new Uri($data['$ref']);
            $data = $this->resolver->extract($this->document, $ref);

            $this->pushStack($ref->getFragment());
            $return = $this->parseParameter($type, $data);
            $this->popStack();

            return $return;
        }

        $name = isset($data['name']) ? $data['name'] : null;
        $in   = isset($data['in'])   ? $data['in']   : null;

        $property = null;
        $required = null;
        if (!empty($name) && $in == $type && is_array($data)) {
            if (isset($data['required'])) {
                $required = (bool) $data['required'];
            } else {
                $required = false;
            }

            if (isset($data['schema']) && is_array($data['schema'])) {
                if (isset($data['schema']['$ref'])) {
                    $property = $this->resolver->resolve($this->document, new Uri($data['schema']['$ref']), null, 0);
                } else {
                    $this->pushPath('schema');

                    $pointer  = $this->getJsonPointer();
                    $property = $this->document->getProperty($pointer);

                    $this->popPath();
                }
            } else {
                $property = Property::get();
            }
        }

        return [
            $name,
            $property,
            $required
        ];
    }

    private function parseRequest(Resource\MethodAbstract $method, array $data)
    {
        if (isset($data['requestBody']) && is_array($data['requestBody'])) {
            $this->pushPath('requestBody');

            $property = $this->getPropertyFromContent($data['requestBody']);
            if ($property instanceof PropertyInterface) {
                $method->setRequest(new Schema($property));
            }

            $this->popPath();
        }
    }

    private function parseResponses(Resource\MethodAbstract $method, array $data)
    {
        if (isset($data['responses']) && is_array($data['responses'])) {
            $this->pushPath('responses');
            
            foreach ($data['responses'] as $statusCode => $row) {
                $statusCode = (int) $statusCode;
                if ($statusCode < 100) {
                    continue;
                }

                $this->pushPath($statusCode);

                $property = $this->getPropertyFromContent($row);
                if ($property instanceof PropertyInterface) {
                    $method->addResponse($statusCode, new Schema($property));
                }

                $this->popPath();
            }
            
            $this->popPath();
        }
    }

    private function getPropertyFromContent(array $data)
    {
        $property = null;
        if (isset($data['$ref'])) {
            $ref  = new Uri($data['$ref']);
            $data = $this->resolver->extract($this->document, $ref);

            $this->pushStack($ref->getFragment());
            $property = $this->getPropertyFromContent($data);
            $this->popStack();

            return $property;
        } elseif (isset($data['content'])) {
            $this->pushPath('content');
            
            $content = $data['content'];
            if (isset($content['application/json'])) {
                $this->pushPath('application/json');

                $mediaType = $content['application/json'];
                if (isset($mediaType['schema'])) {
                    if (isset($mediaType['schema']['$ref'])) {
                        $property = $this->resolver->resolve($this->document, new Uri($mediaType['schema']['$ref']), null, 0);
                    } else {
                        $this->pushPath('schema');

                        $pointer  = $this->getJsonPointer();
                        $property = $this->document->getProperty($pointer);

                        $this->popPath();
                    }
                }

                $this->popPath();
            }

            $this->popPath();
        }

        return $property;
    }

    private function pushPath($path)
    {
        array_push($this->pathStack[$this->stackIndex], $path);
    }

    private function popPath()
    {
        array_pop($this->pathStack[$this->stackIndex]);
    }

    private function pushStack($fragment)
    {
        $this->stackIndex++;
        array_push($this->pathStack, array_filter(explode('/', $fragment)));
    }

    private function popStack()
    {
        $this->stackIndex--;
        array_pop($this->pathStack);
    }

    private function getJsonPointer()
    {
        return '/' . implode('/', array_map(function ($path) {
            return str_replace(['~', '/'], ['~0', '~1'], $path);
        }, $this->pathStack[$this->stackIndex]));
    }

    private function setUp($schema)
    {
        $this->data = Parser::decode($schema, true);

        $this->pathStack  = [[]];
        $this->stackIndex = 0;

        $this->document = new JsonSchema\Document($this->data, $this->resolver, $this->basePath);
        $this->resolver->setRootDocument($this->document);
    }

    private function getPaths()
    {
        return isset($this->data['paths']) ? $this->data['paths'] : [];
    }

    public static function fromFile($file, $path)
    {
        if (!empty($file) && is_file($file)) {
            $basePath = pathinfo($file, PATHINFO_DIRNAME);
            $parser   = new OpenAPI($basePath);

            return $parser->parse(file_get_contents($file), $path);
        } else {
            throw new RuntimeException('Could not load OpenAPI schema ' . $file);
        }
    }
}
