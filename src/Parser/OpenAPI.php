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

use PSX\Api\ParserInterface;
use PSX\Api\Resource;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\Schema\Parser\JsonSchema;
use PSX\Schema\Property;
use PSX\Schema\PropertyInterface;
use PSX\Schema\Schema;
use PSX\Schema\SchemaInterface;
use PSX\Uri\Uri;
use RuntimeException;

/**
 * OpenAPI
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OpenAPI implements ParserInterface
{
    /**
     * @var string|null
     */
    protected $basePath;

    /**
     * @var \PSX\Schema\Parser\JsonSchema\Document
     */
    protected $document;

    /**
     * @var \PSX\Schema\Parser\JsonSchema\RefResolver
     */
    protected $resolver;

    /**
     * @var array
     */
    protected $pathStack;

    /**
     * @param string $basePath
     * @param \Symfony\Component\Yaml\Parser|null $parser
     */
    public function __construct($basePath = null)
    {
        $this->basePath = $basePath;
    }

    /**
     * @inheritdoc
     */
    public function parse($schema, $path)
    {
        $data  = Parser::decode($schema, true);
        $paths = isset($data['paths']) ? $data['paths'] : [];

        $this->pathStack = [];
        $this->resolver  = JsonSchema\RefResolver::createDefault();
        $this->document  = new JsonSchema\Document($data, $this->resolver, $this->basePath);

        $normalizedPath = Inflection::transformRoutePlaceholder($path);
        
        if (isset($paths[$normalizedPath])) {
            return $this->parseResource($paths[$normalizedPath], $normalizedPath);
        } else {
            throw new RuntimeException('Could not find resource definition "' . $normalizedPath . '" in OpenAPI schema');
        }
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

                if (isset($operation['description'])) {
                    $method->setDescription($operation['description']);
                }

                $this->parseQueryParameters($method, $operation);
                $this->parseRequest($method, $operation);
                $this->parseResponses($method, $operation);

                $resource->addMethod($method);

                $this->popPath();
            }
        }

        return $resource;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param array $data
     */
    private function parseUriParameters(Resource $resource, array $data)
    {
        $this->pushPath('parameters');

        if (isset($data['parameters']) && is_array($data['parameters'])) {
            $required = [];
            foreach ($data['parameters'] as $index => $definition) {
                $this->pushPath($index);
                
                $name = isset($definition['name']) ? $definition['name'] : null;
                $in   = isset($definition['in'])   ? $definition['in']   : null;

                if (!empty($name) && $in == 'path' && is_array($definition)) {
                    if (isset($definition['required'])) {
                        $isRequired = (bool) $definition['required'];
                    } else {
                        $isRequired = false;
                    }

                    if (isset($definition['schema']) && is_array($definition['schema'])) {
                        if (isset($definition['schema']['$ref'])) {
                            $property = $this->resolver->resolve($this->document, new Uri($definition['schema']['$ref']), null, 0);
                        } else {
                            $this->pushPath('schema');

                            $pointer  = $this->getJsonPointer();
                            $property = $this->document->getProperty($pointer);

                            $this->popPath();
                        }
                    } else {
                        $property = Property::get();
                    }

                    $resource->addPathParameter($name, $property);

                    if ($isRequired) {
                        $required[] = $name;
                    }
                }
                
                $this->popPath();
            }

            $resource->getPathParameters()->setRequired($required);
        }

        $this->popPath();
    }

    /**
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @param array $data
     */
    private function parseQueryParameters(Resource\MethodAbstract $method, array $data)
    {
        $this->pushPath('parameters');

        if (isset($data['parameters']) && is_array($data['parameters'])) {
            $required = [];
            foreach ($data['parameters'] as $index => $definition) {
                $this->pushPath($index);

                $name = isset($definition['name']) ? $definition['name'] : null;
                $in   = isset($definition['in'])   ? $definition['in']   : null;

                if (!empty($name) && $in == 'query' && is_array($definition)) {
                    if (isset($definition['required'])) {
                        $isRequired = (bool) $definition['required'];
                    } else {
                        $isRequired = false;
                    }

                    if (isset($definition['schema']) && is_array($definition['schema'])) {
                        if (isset($definition['schema']['$ref'])) {
                            $property = $this->resolver->resolve($this->document, new Uri($definition['schema']['$ref']), null, 0);
                        } else {
                            $this->pushPath('schema');

                            $pointer  = $this->getJsonPointer();
                            $property = $this->document->getProperty($pointer);

                            $this->popPath();                        }
                    } else {
                        $property = Property::get();
                    }

                    $method->addQueryParameter($name, $property);

                    if ($isRequired) {
                        $required[] = $name;
                    }
                }

                $this->popPath();
            }

            $method->getQueryParameters()->setRequired($required);
        }

        $this->popPath();
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
            $property = $this->resolver->resolve($this->document, new Uri($data['$ref']), null, 0);
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
        array_push($this->pathStack, $path);
    }

    private function popPath()
    {
        array_pop($this->pathStack);
    }

    private function getJsonPointer()
    {
        return '/' . implode('/', array_map(function($path){
            return str_replace(['~', '/'], ['~0', '~1'], $path);
        }, $this->pathStack));
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
