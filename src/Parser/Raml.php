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
use PSX\Schema\Parser\JsonSchema;
use PSX\Schema\Property;
use PSX\Schema\SchemaInterface;
use RuntimeException;
use Symfony\Component\Yaml\Parser;

/**
 * Raml
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Raml implements ParserInterface, ParserCollectionInterface
{
    /**
     * @var string|null
     */
    private $basePath;

    /**
     * @var \Symfony\Component\Yaml\Parser
     */
    private $parser;

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $schemas;

    /**
     * @param string $basePath
     * @param \Symfony\Component\Yaml\Parser|null $parser
     */
    public function __construct($basePath = null, Parser $parser = null)
    {
        $this->basePath = $basePath;
        $this->parser   = $parser ?: new Parser();
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
            throw new RuntimeException('Could not find resource definition "' . $path . '" in RAML schema');
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
        $status   = Resource::STATUS_ACTIVE;
        $resource = new Resource($status, $path);

        if (isset($data['displayName'])) {
            $resource->setTitle($data['displayName']);
        }

        if (isset($data['description'])) {
            $resource->setDescription($data['description']);
        }

        $this->parseUriParameters($resource, $data);

        $mergedTrait = array();
        if (isset($data['is']) && is_array($data['is'])) {
            foreach ($data['is'] as $traitName) {
                $trait = $this->getTrait($traitName);
                if (is_array($trait)) {
                    $mergedTrait = array_merge_recursive($mergedTrait, $trait);
                }
            }
        }

        foreach ($data as $methodName => $row) {
            if (in_array($methodName, ['get', 'post', 'put', 'delete', 'patch']) && is_array($row)) {
                if (!empty($mergedTrait)) {
                    $row = array_merge_recursive($row, $mergedTrait);
                }

                $method = Resource\Factory::getMethod(strtoupper($methodName));

                if (isset($row['description'])) {
                    $method->setDescription($row['description']);
                }

                $this->parseQueryParameters($method, $row);
                $this->parseRequest($method, $row);
                $this->parseResponses($method, $row);

                $resource->addMethod($method);
            }
        }

        return $resource;
    }

    private function getTrait($name)
    {
        if (isset($this->data['traits']) && is_array($this->data['traits'])) {
            foreach ($this->data['traits'] as $trait) {
                if (is_array($trait) && isset($trait[$name])) {
                    return $this->parseDefinition($trait[$name]);
                }
            }
        }

        return null;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param array $data
     */
    private function parseUriParameters(Resource $resource, array $data)
    {
        list($properties, $required) = $this->parseParameters('uriParameters', $data);

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
        list($properties, $required) = $this->parseParameters('queryParameters', $data);

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
     */
    private function parseParameters($type, array $data)
    {
        $properties = [];
        $required   = [];

        if (isset($data[$type]) && is_array($data[$type])) {
            $required = [];
            foreach ($data[$type] as $name => $definition) {
                if (!empty($name) && is_array($definition)) {
                    list($property, $isRequired) = $this->parseParameter($definition);

                    if ($property !== null) {
                        $properties[$name] = $property;
                    }

                    if ($isRequired !== null && $isRequired === true) {
                        $required[] = $name;
                    }
                }
            }
        }

        return [
            $properties,
            $required,
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    private function parseParameter(array $data)
    {
        $property = null;
        $required = null;

        if (is_array($data)) {
            if (isset($data['required'])) {
                $required = (bool) $data['required'];
            } else {
                $required = false;
            }

            $property = $this->getParameter($data);
        }

        return [
            $property,
            $required
        ];
    }

    /**
     * @param array $definition
     * @return \PSX\Schema\PropertyInterface
     */
    private function getParameter(array $definition)
    {
        $type     = isset($definition['type']) ? $definition['type'] : 'string';
        $property = $this->getPropertyType($type);

        if (isset($definition['description'])) {
            $property->setDescription($definition['description']);
        }

        if (isset($definition['enum']) && is_array($definition['enum'])) {
            $property->setEnum($definition['enum']);
        }

        if (isset($definition['pattern'])) {
            $property->setPattern($definition['pattern']);
        }

        if (isset($definition['minLength'])) {
            $property->setMinLength($definition['minLength']);
        }

        if (isset($definition['maxLength'])) {
            $property->setMaxLength($definition['maxLength']);
        }

        if (isset($definition['minimum'])) {
            $property->setMinimum($definition['minimum']);
        }

        if (isset($definition['maximum'])) {
            $property->setMaximum($definition['maximum']);
        }

        if (isset($definition['multipleOf'])) {
            $property->setMultipleOf($definition['multipleOf']);
        }

        return $property;
    }

    private function parseRequest(Resource\MethodAbstract $method, array $data)
    {
        if (isset($data['body']) && is_array($data['body'])) {
            $schema = $this->getBodySchema($data['body']);

            if ($schema instanceof SchemaInterface) {
                $method->setRequest($schema);
            }
        }
    }

    private function parseResponses(Resource\MethodAbstract $method, array $data)
    {
        if (isset($data['responses']) && is_array($data['responses'])) {
            foreach ($data['responses'] as $statusCode => $row) {
                $statusCode = (int) $statusCode;
                if ($statusCode < 100) {
                    continue;
                }

                if (isset($row['body']) && is_array($row['body'])) {
                    $schema = $this->getBodySchema($row['body']);

                    if ($schema instanceof SchemaInterface) {
                        $method->addResponse($statusCode, $schema);
                    }
                }
            }
        }
    }

    private function getBodySchema(array $body)
    {
        foreach ($body as $contentType => $row) {
            if ($contentType == 'application/json' && is_array($row)) {
                $schema = null;
                if (isset($row['schema'])) { // 0.8
                    $schema = $row['schema'];
                } elseif (isset($row['type'])) { // 1.0
                    $schema = $row['type'];
                }

                if (!empty($schema)) {
                    return $this->parseSchema($schema);
                }
            }
        }

        return null;
    }

    /**
     * @param mixed $schema
     * @return \PSX\Schema\SchemaInterface
     */
    private function parseSchema($schema)
    {
        if (is_string($schema)) {
            if (substr($schema, 0, 8) == '!include') {
                $file = trim(substr($schema, 8));
                if (!is_file($file)) {
                    $file = $this->basePath . '/' . $file;
                }

                return JsonSchema::fromFile($file);
            } elseif (strpos($schema, '{') !== false) {
                $parser = new JsonSchema($this->basePath);

                return $parser->parse($schema);
            } elseif (isset($this->schemas[$schema])) {
                return $this->parseSchema($this->schemas[$schema]);
            } else {
                throw new RuntimeException('Referenced schema does not exist');
            }
        } elseif (is_array($schema)) {
            $parser = new JsonSchema($this->basePath);

            return $parser->parse(json_encode($schema));
        } else {
            throw new RuntimeException('Schema definition must be a string or object');
        }
    }

    private function parseDefinition($definition)
    {
        if (is_string($definition) && substr($definition, 0, 8) == '!include') {
            $file = trim(substr($definition, 8));

            if (!is_file($file)) {
                $file = $this->basePath !== null ? $this->basePath . DIRECTORY_SEPARATOR . $file : $file;
            }

            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if (in_array($extension, ['raml', 'yml', 'yaml'])) {
                return $this->parser->parse(file_get_contents($file));
            } elseif (in_array($extension, ['json'])) {
                return json_decode(file_get_contents($file), true);
            } else {
                return file_get_contents($file);
            }
        } else {
            return $definition;
        }
    }

    private function getPropertyType($type)
    {
        switch ($type) {
            case 'integer':
                return Property::getInteger();

            case 'number':
                return Property::getNumber();

            case 'date':
            case 'datetime':
            case 'datetime-only':
                return Property::getDateTime();

            case 'date-only':
                return Property::getDate();

            case 'time-only':
                return Property::getTime();

            case 'boolean':
                return Property::getBoolean();

            case 'string':
            default:
                return Property::getString();
        }
    }

    private function setUp($schema)
    {
        $this->data    = $this->parser->parse($schema);
        $this->schemas = $this->getSchemas();
    }

    private function getPaths()
    {
        return $this->flattenPaths($this->data);
    }

    private function flattenPaths(array $data, $basePath = null)
    {
        $paths = [];

        foreach ($data as $path => $row) {
            if (isset($path[0]) && $path[0] == '/') {
                $subPaths = [];
                $result   = [];
                foreach ($row as $key => $value) {
                    if (isset($key[0]) && $key[0] == '/') {
                        $subPaths[$key] = $value;
                    } else {
                        $result[$key] = $value;
                    }
                }

                $paths[$basePath . $path] = $result;

                $paths = array_merge($paths, $this->flattenPaths($subPaths, $path));
            }
        }

        return $paths;
    }

    private function getSchemas()
    {
        if (isset($this->data['schemas']) && is_array($this->data['schemas'])) { // 0.8
            return $this->parseSchemas($this->data['schemas']);
        } elseif (isset($this->data['types']) && is_array($this->data['types'])) { // 1.0
            return $this->parseSchemas($this->data['types']);
        }

        return [];
    }

    private function parseSchemas(array $schemas)
    {
        // @TODO handle !include in schemas

        if (isset($schemas[0])) {
            foreach ($schemas as $subSchema) {
                if (is_string($subSchema)) {
                } elseif (is_array($subSchema)) {
                    return $subSchema;
                }
            }
        } else {
            return $schemas;
        }

        return [];
    }

    public static function fromFile($file, $path)
    {
        if (!empty($file) && is_file($file)) {
            $basePath = pathinfo($file, PATHINFO_DIRNAME);
            $parser   = new Raml($basePath);

            return $parser->parse(file_get_contents($file), $path);
        } else {
            throw new RuntimeException('Could not load RAML schema ' . $file);
        }
    }
}
