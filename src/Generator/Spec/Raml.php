<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2019 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Generator\Spec;

use PSX\Api\GeneratorAbstract;
use PSX\Api\GeneratorCollectionInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\SpecificationInterface;
use PSX\Api\Util\Inflection;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\PropertyInterface;
use PSX\Schema\PropertyType;
use PSX\Schema\Type\BooleanType;
use PSX\Schema\Type\IntegerType;
use PSX\Schema\Type\NumberType;
use PSX\Schema\Type\ScalarType;
use PSX\Schema\Type\StringType;
use PSX\Schema\Type\StructType;
use PSX\Schema\Type\TypeAbstract;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;
use Symfony\Component\Yaml\Inline;
use Symfony\Component\Yaml\Yaml;

/**
 * Raml
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Raml extends GeneratorAbstract
{
    /**
     * @var integer
     */
    protected $apiVersion;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @param integer $apiVersion
     * @param string $baseUri
     */
    public function __construct($apiVersion, $baseUri)
    {
        $this->apiVersion = $apiVersion;
        $this->baseUri    = $baseUri;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @inheritDoc
     */
    public function generate(SpecificationInterface $specification)
    {
        $collection = $specification->getResourceCollection();
        $definitions = $specification->getDefinitions();

        $raml = $this->getDeclaration();
        foreach ($collection as $path => $resource) {
            $raml.= $this->getResource($resource, $definitions);
        }

        $raml.= $this->getTypes($definitions);

        return $raml;
    }

    /**
     * @return string
     */
    protected function getDeclaration()
    {
        $raml = '#%RAML 1.0' . "\n";
        $raml.= '---' . "\n";
        $raml.= 'baseUri: ' . Inline::dump($this->baseUri) . "\n";
        $raml.= 'version: v' . $this->apiVersion . "\n";
        $raml.= 'title: ' . Inline::dump($this->title ?: 'PSX') . "\n";

        if (!empty($this->description)) {
            $raml.= 'description: ' . Inline::dump($this->description) . "\n";
        }

        return $raml;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param \PSX\Schema\DefinitionsInterface $definitions
     * @return string
     */
    protected function getResource(Resource $resource, DefinitionsInterface $definitions)
    {
        $path = Inflection::transformRoutePlaceholder($resource->getPath() ?: '/');
        $raml = $path . ':' . "\n";

        $description = $resource->getDescription();
        if (!empty($description)) {
            $raml.= '  description: ' . Inline::dump($description) . "\n";
        }

        $pathParameters = $resource->getPathParameters();
        if (!empty($pathParameters) && $definitions->hasType($pathParameters)) {
            $raml.= $this->getParameters('uriParameters', $definitions->getType($pathParameters), 2);
        }

        $methods = $resource->getMethods();
        foreach ($methods as $method) {
            $raml.= '  ' . strtolower($method->getName()) . ':' . "\n";

            // description
            $description = $method->getDescription();
            if (!empty($description)) {
                $raml.= '    description: ' . Inline::dump($description) . "\n";
            }

            // query parameter
            $queryParameters = $method->getQueryParameters();
            if (!empty($queryParameters) && $definitions->hasType($queryParameters)) {
                $raml.= $this->getParameters('queryParameters', $definitions->getType($queryParameters), 4);
            }

            // request body
            $request = $method->hasRequest();
            if (!empty($request)) {
                $raml.= '    body:' . "\n";
                $raml.= '      application/json:' . "\n";
                $raml.= '        type: ' . $request . "\n";
            }

            // response body
            $responses = $method->getResponses();
            if (!empty($responses)) {
                $raml.= '    responses:' . "\n";
                foreach ($responses as $statusCode => $response) {
                    $raml.= '      ' . $statusCode . ':' . "\n";
                    $raml.= '        body:' . "\n";
                    $raml.= '          application/json:' . "\n";
                    $raml.= '            type: ' . $response . "\n";
                }
            }
        }

        return $raml;
    }

    private function getTypes(DefinitionsInterface $definitions): string
    {
        $generator = new Generator\JsonSchema('#/types/');
        $result    = $generator->toArray(TypeFactory::getAny(), $definitions);

        $raml = 'types:' . "\n";
        foreach ($result['definitions'] as $name => $schema) {
            $raml.= '  ' . $name . ': ' . Yaml::dump($schema, 0, 2) . "\n";
        }

        return $raml;
    }
    
    protected function getParameters(string $name, TypeInterface $type, int $indent): string
    {
        if (!$type instanceof StructType) {
            return '';
        }

        $properties = $type->getProperties();
        if (empty($properties)) {
            return '';
        }

        $raml = str_repeat(' ', $indent) . $name . ':' . "\n";

        foreach ($properties as $name => $parameter) {
            $raml.= str_repeat(' ', $indent + 2) . $name . ':' . "\n";
            $raml.= $this->getParameter($parameter, $indent + 4, in_array($name, $type->getRequired() ?: []));
        }

        return $raml;
    }

    /**
     * @param \PSX\Schema\TypeInterface $type
     * @param string $indent
     * @param boolean $required
     */
    protected function getParameter(TypeInterface $type, $indent, $required)
    {
        $raml   = '';
        $indent = str_repeat(' ', $indent);

        if ($type instanceof TypeAbstract) {
            $description = $type->getDescription();
            if (!empty($description)) {
                $raml.= $indent . 'description: ' . Inline::dump($type->getDescription()) . "\n";
            }
        }

        if ($type instanceof IntegerType) {
            $raml.= $indent . 'type: integer' . "\n";
        } elseif ($type instanceof NumberType) {
            $raml.= $indent . 'type: number' . "\n";
        } elseif ($type instanceof BooleanType) {
            $raml.= $indent . 'type: boolean' . "\n";
        } elseif ($type instanceof StringType) {
            if ($type->getFormat() === TypeAbstract::FORMAT_DATE) {
                $raml.= $indent . 'type: date-only' . "\n";
            } elseif ($type->getFormat() === TypeAbstract::FORMAT_DATETIME) {
                $raml.= $indent . 'type: datetime-only' . "\n";
            } elseif ($type->getFormat() === TypeAbstract::FORMAT_TIME) {
                $raml.= $indent . 'type: time-only' . "\n";
            } else {
                $raml.= $indent . 'type: string' . "\n";
            }

            // string
            $minLength = $type->getMinLength();
            if ($minLength !== null) {
                $raml.= $indent . 'minLength: ' . $minLength . "\n";
            }

            $maxLength = $type->getMaxLength();
            if ($maxLength !== null) {
                $raml.= $indent . 'maxLength: ' . $maxLength . "\n";
            }

            $pattern = $type->getPattern();
            if (!empty($pattern)) {
                $raml.= $indent . 'pattern: ' . Inline::dump($pattern) . "\n";
            }
        }

        if ($type instanceof ScalarType) {
            $enum = $type->getEnum();
            if (!empty($enum)) {
                $raml.= $indent . 'enum: ' . Inline::dump($enum) . "\n";
            }
        }

        if ($type instanceof NumberType) {
            $minimum = $type->getMinimum();
            if ($minimum !== null) {
                $raml.= $indent . 'minimum: ' . $minimum . "\n";
            }

            $maximum = $type->getMaximum();
            if ($maximum !== null) {
                $raml.= $indent . 'maximum: ' . $maximum . "\n";
            }

            $multipleOf = $type->getMultipleOf();
            if ($multipleOf !== null) {
                $raml.= $indent . 'multipleOf: ' . $multipleOf . "\n";
            }
        }

        $raml.= $indent . 'required: ' . ($required ? 'true' : 'false') . "\n";

        return $raml;
    }
}
