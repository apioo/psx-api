<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Generator\Client;

use PSX\Api\GeneratorInterface;
use PSX\Api\Resource;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;
use PSX\Schema\Property;
use PSX\Schema\PropertyType;
use PSX\Schema\Schema;
use PSX\Schema\SchemaInterface;

/**
 * LanguageAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class LanguageAbstract implements GeneratorInterface
{
    use Generator\GeneratorTrait;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @inheritdoc
     */
    public function generate(Resource $resource)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/Language');
        $engine = new \Twig_Environment($loader);

        $properties = $this->getProperties($resource);
        $urlParts = $this->getUrlParts($resource, $properties);

        $schemas = [];
        $methods = [];
        foreach ($resource->getMethods() as $method) {
            $methodName = $method->getOperationId() ?: strtolower($method->getName());

            $args = [];

            // query parameters
            if ($method->hasQueryParameters()) {
                $parameters = $method->getQueryParameters();

                $schemas[$parameters->getTitle()] = $parameters;
                $args['query'] = $parameters->getTitle();
            }

            // request
            $request = $method->getRequest();
            if ($request instanceof SchemaInterface && !in_array($method->getName(), ['GET', 'DELETE'])) {
                $property = $request->getDefinition();
                $name     = $this->getIdentifierForProperty($property);

                $schemas[$name] = $property;
                $args['data'] = $name;
            }

            // response
            $response = $this->getSuccessfulResponse($method);
            if ($response instanceof SchemaInterface) {
                $property = $response->getDefinition();
                $name     = $this->getIdentifierForProperty($property);

                $schemas[$name] = $property;
                $return = $name;
            } else {
                $return = null;
            }

            $methods[$methodName] = [
                'httpMethod' => $method->getName(),
                'description' => $method->getDescription(),
                'secure' => $method->hasSecurity(),
                'args' => $args,
                'return' => $return,
            ];
        }

        $namespace = $this->getClassName($resource->getPath());
        $schemas = $this->generateSchema($schemas);

        return $engine->render($this->getTemplate(), [
            'namespace' => $namespace,
            'base_url' => $this->baseUrl,
            'url_parts' => $urlParts,
            'resource' => $resource,
            'properties' => $properties,
            'methods' => $methods,
            'schemas' => $schemas,
        ]);
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param array $args
     * @return array
     */
    protected function getUrlParts(Resource $resource, array $args): array
    {
        $result = [];
        reset($args);
        $parts = explode('/', $resource->getPath());
        foreach ($parts as $part) {
            if (isset($part[0]) && ($part[0] == ':' || $part[0] == '$')) {
                $pathName = key($args);
                if ($pathName === null) {
                    throw new \RuntimeException('Missing ' . $part . ' as path parameter');
                }

                $result[] = [
                    'type'  => 'variable',
                    'value' => $pathName,
                ];
                next($args);
            } elseif (!empty($part)) {
                $result[] = [
                    'type'  => 'string',
                    'value' => $part,
                ];
            }
        }

        return $result;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return array
     */
    protected function getProperties(Resource $resource)
    {
        $args = [];
        if ($resource->hasPathParameters()) {
            $properties = $resource->getPathParameters()->getProperties();
            foreach ($properties as $name => $property) {
                $args[$name] = $this->getType($property);
            }
        }

        return $args;
    }

    /**
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @return \PSX\Schema\SchemaInterface|null
     */
    protected function getSuccessfulResponse(Resource\MethodAbstract $method)
    {
        $responses = $method->getResponses();
        $codes = [200, 201];

        foreach ($codes as $code) {
            if (isset($responses[$code])) {
                return $responses[$code];
            }
        }

        return null;
    }

    /**
     * @return string
     */
    protected function generateSchema(array $schemas)
    {
        $prop = Property::getObject();
        $prop->setTitle('Endpoint');
        foreach ($schemas as $name => $property) {
            $prop->addProperty($name, $property);
        }

        return $this->getGenerator()->generate(new Schema($prop));
    }

    /**
     * Returns the type of the provided property for the specific language
     *
     * @param \PSX\Schema\PropertyType $property
     * @return string
     */
    abstract protected function getType(PropertyType $property): string;

    /**
     * @return string
     */
    abstract protected function getTemplate(): string;

    /**
     * @return \PSX\Schema\GeneratorInterface
     */
    abstract protected function getGenerator(): SchemaGeneratorInterface;

    /**
     * @param string $path
     * @return string
     */
    private function getClassName($path): string
    {
        $parts = explode('/', $path);
        $parts = array_map(function($part){
            return ucfirst(preg_replace('/[^A-Za-z0-9_]+/', '', $part));
        }, $parts);

        return implode('', $parts);
    }
}
