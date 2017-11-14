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

namespace PSX\Api\Generator;

use PSX\Api\GeneratorAbstract;
use PSX\Api\GeneratorCollectionInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\Util\Inflection;
use PSX\Schema\Generator;
use PSX\Schema\PropertyInterface;
use PSX\Schema\PropertyType;
use Symfony\Component\Yaml\Inline;

/**
 * Raml
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Raml extends GeneratorAbstract implements GeneratorCollectionInterface
{
    use Generator\GeneratorTrait;

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
    protected $targetNamespace;

    /**
     * @var string
     */
    protected $title;

    /**
     * @param integer $apiVersion
     * @param string $baseUri
     * @param string $targetNamespace
     */
    public function __construct($apiVersion, $baseUri, $targetNamespace)
    {
        $this->apiVersion      = $apiVersion;
        $this->baseUri         = $baseUri;
        $this->targetNamespace = $targetNamespace;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    public function generate(Resource $resource)
    {
        $raml = $this->getDeclaration();
        $raml.= $this->getResource($resource);

        return $raml;
    }

    /**
     * @param \PSX\Api\ResourceCollection $collection
     * @return string
     */
    public function generateAll(ResourceCollection $collection)
    {
        $raml = $this->getDeclaration();

        foreach ($collection as $path => $resource) {
            $raml.= $this->getResource($resource);
        }

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

        return $raml;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    protected function getResource(Resource $resource)
    {
        $path = Inflection::transformRoutePlaceholder($resource->getPath() ?: '/');
        $raml = $path . ':' . "\n";

        $description = $resource->getDescription();
        if (!empty($description)) {
            $raml.= '  description: ' . Inline::dump($description) . "\n";
        }

        // path parameter
        $pathParameters = $resource->getPathParameters();
        $properties     = $pathParameters->getProperties();

        if (!empty($properties)) {
            $raml.= '  uriParameters:' . "\n";

            foreach ($properties as $name => $parameter) {
                $raml.= '    ' . $name . ':' . "\n";
                $raml.= $this->getParameter($parameter, 6, in_array($name, $pathParameters->getRequired() ?: []));
            }
        }

        $generator = new Generator\JsonSchema($this->targetNamespace);
        $methods   = $resource->getMethods();

        foreach ($methods as $method) {
            $raml.= '  ' . strtolower($method->getName()) . ':' . "\n";

            // description
            $description = $method->getDescription();
            if (!empty($description)) {
                $raml.= '    description: ' . Inline::dump($description) . "\n";
            }

            // query parameter
            $queryParameters = $method->getQueryParameters();
            $properties      = $queryParameters->getProperties();

            if (!empty($properties)) {
                $raml.= '    queryParameters:' . "\n";

                foreach ($properties as $name => $parameter) {
                    $raml.= '      ' . $name . ':' . "\n";
                    $raml.= $this->getParameter($parameter, 8, in_array($name, $queryParameters->getRequired() ?: []));
                }
            }

            // request body
            if ($method->hasRequest()) {
                $schema = $generator->generate($method->getRequest());
                $schema = str_replace("\n", "\n          ", $schema);

                $raml.= '    body:' . "\n";
                $raml.= '      application/json:' . "\n";
                $raml.= '        type: |' . "\n";
                $raml.= '          ' . $schema . "\n";
            }

            // response body
            $raml.= '    responses:' . "\n";

            $responses = $method->getResponses();

            foreach ($responses as $statusCode => $response) {
                $schema = $generator->generate($response);
                $schema = str_replace("\n", "\n              ", $schema);

                $raml.= '      ' . $statusCode . ':' . "\n";
                $raml.= '        body:' . "\n";
                $raml.= '          application/json:' . "\n";
                $raml.= '            type: |' . "\n";
                $raml.= '              ' . $schema . "\n";
            }
        }

        return $raml;
    }

    /**
     * @param \PSX\Schema\PropertyInterface $parameter
     * @param string $indent
     * @param boolean $required
     */
    protected function getParameter(PropertyInterface $parameter, $indent, $required)
    {
        $raml   = '';
        $indent = str_repeat(' ', $indent);
        $type   = $this->getRealType($parameter);
        
        switch ($type) {
            case PropertyType::TYPE_INTEGER:
                $raml.= $indent . 'type: integer' . "\n";
                break;

            case PropertyType::TYPE_NUMBER:
                $raml.= $indent . 'type: number' . "\n";
                break;

            case PropertyType::TYPE_BOOLEAN:
                $raml.= $indent . 'type: boolean' . "\n";
                break;

            case PropertyType::TYPE_NULL:
                $raml.= $indent . 'type: null' . "\n";
                break;

            case PropertyType::TYPE_STRING:
            default:
                if ($parameter->getFormat() === PropertyType::FORMAT_DATE) {
                    $raml.= $indent . 'type: date-only' . "\n";
                } elseif ($parameter->getFormat() === PropertyType::FORMAT_DATETIME) {
                    $raml.= $indent . 'type: datetime-only' . "\n";
                } elseif ($parameter->getFormat() === PropertyType::FORMAT_TIME) {
                    $raml.= $indent . 'type: time-only' . "\n";
                } else {
                    $raml.= $indent . 'type: string' . "\n";
                }
                break;
        }

        $description = $parameter->getDescription();

        if (!empty($description)) {
            $raml.= $indent . 'description: ' . Inline::dump($parameter->getDescription()) . "\n";
        }

        $raml.= $indent . 'required: ' . ($required ? 'true' : 'false') . "\n";

        // string
        $minLength = $parameter->getMinLength();
        if ($minLength !== null) {
            $raml.= $indent . 'minLength: ' . $minLength . "\n";
        }

        $maxLength = $parameter->getMaxLength();
        if ($maxLength !== null) {
            $raml.= $indent . 'maxLength: ' . $maxLength . "\n";
        }

        $pattern = $parameter->getPattern();
        if (!empty($pattern)) {
            $raml.= $indent . 'pattern: ' . Inline::dump($pattern) . "\n";
        }

        // number
        $minimum = $parameter->getMinimum();
        if ($minimum !== null) {
            $raml.= $indent . 'minimum: ' . $minimum . "\n";
        }

        $maximum = $parameter->getMaximum();
        if ($maximum !== null) {
            $raml.= $indent . 'maximum: ' . $maximum . "\n";
        }

        $multipleOf = $parameter->getMultipleOf();
        if ($multipleOf !== null) {
            $raml.= $indent . 'multipleOf: ' . $multipleOf . "\n";
        }

        // common
        $enum = $parameter->getEnum();
        if (!empty($enum)) {
            $raml.= $indent . 'enum: ' . Inline::dump($enum) . "\n";
        }
        
        return $raml;
    }
}
