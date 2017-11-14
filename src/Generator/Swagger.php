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

use Doctrine\Common\Annotations\Reader;
use PSX\Api\GeneratorAbstract;
use PSX\Api\GeneratorCollectionInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\Model\Swagger\Info;
use PSX\Model\Swagger\Operation;
use PSX\Model\Swagger\Parameter;
use PSX\Model\Swagger\Path;
use PSX\Model\Swagger\Paths;
use PSX\Model\Swagger\Response;
use PSX\Model\Swagger\Responses;
use PSX\Model\Swagger\Swagger as Declaration;
use PSX\Schema\Generator;
use PSX\Schema\Generator\GeneratorTrait;
use PSX\Schema\Parser\Popo\Dumper;
use PSX\Schema\PropertyInterface;
use PSX\Schema\SchemaInterface;

/**
 * Generates a Swagger 2.0 representation of an API resource
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Swagger extends GeneratorAbstract implements GeneratorCollectionInterface
{
    use GeneratorTrait;

    /**
     * @var \PSX\Schema\Parser\Popo\Dumper
     */
    protected $dumper;

    /**
     * @var string
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
     * @param \Doctrine\Common\Annotations\Reader $reader
     * @param integer $apiVersion
     * @param string $baseUri
     * @param string $targetNamespace
     */
    public function __construct(Reader $reader, $apiVersion, $baseUri, $targetNamespace)
    {
        $this->dumper          = new Dumper($reader);
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
        $paths   = new Paths();
        $schemas = new \stdClass();

        $this->buildDefinitions($resource, $schemas);
        $this->buildPaths($resource, $paths);

        return $this->buildDeclaration($paths, $schemas);
    }

    /**
     * @param \PSX\Api\ResourceCollection $collection
     * @return string
     */
    public function generateAll(ResourceCollection $collection)
    {
        $paths   = new Paths();
        $schemas = new \stdClass();

        foreach ($collection as $path => $resource) {
            $this->buildDefinitions($resource, $schemas);
            $this->buildPaths($resource, $paths, $this->getIdFromPath($resource->getPath()));
        }

        return $this->buildDeclaration($paths, $schemas);
    }

    /**
     * @param \PSX\Model\Swagger\Paths $paths
     * @param \stdClass $schemas
     * @return string
     */
    protected function buildDeclaration(Paths $paths, \stdClass $schemas)
    {
        $info = new Info();
        $info->setTitle($this->title ?: 'PSX');
        $info->setVersion($this->apiVersion);

        $parts  = parse_url($this->baseUri);
        $scheme = $parts['scheme'] ?? null;
        $host   = $parts['host'] ?? null;
        $port   = $parts['port'] ?? null;
        $path   = $parts['path'] ?? null;

        $swagger = new Declaration();
        $swagger->setInfo($info);

        if (!empty($host)) {
            $swagger->setHost($host . (!empty($port) ? ':' . $port : ''));
        }

        $swagger->setBasePath($path ?: '/');
        $swagger->setSchemes(!empty($scheme) ? [$scheme] : ['http', 'https']);
        $swagger->setPaths($paths);
        $swagger->setDefinitions($schemas);

        $data = $this->dumper->dump($swagger);
        $data = Parser::encode($data, JSON_PRETTY_PRINT);

        return $data;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param \PSX\Model\Swagger\Paths $paths
     * @param string $operationPrefix
     */
    protected function buildPaths(Resource $resource, Paths $paths, $operationPrefix = null)
    {
        $path = new Path();

        // path parameter
        $pathParameters = $resource->getPathParameters();
        $parameters     = [];
        $properties     = $pathParameters->getProperties() ?: [];
        foreach ($properties as $name => $parameter) {
            $param = $this->getParameter($parameter, true);
            $param->setName($name);
            $param->setIn('path');

            $parameters[] = $param;
        }

        $path->setParameters($parameters);

        $methods = $resource->getMethods();
        foreach ($methods as $method) {
            // get operation name
            $request     = $method->getRequest();
            $response    = $this->getSuccessfulResponse($method);
            $description = $method->getDescription();
            $operationId = $method->getOperationId();

            if (empty($operationId)) {
                if ($request instanceof SchemaInterface) {
                    $operationId = strtolower($method->getName()) . ucfirst($this->getIdentifierForProperty($request->getDefinition()));
                } elseif ($response instanceof SchemaInterface) {
                    $operationId = strtolower($method->getName()) . ucfirst($this->getIdentifierForProperty($response->getDefinition()));
                }
            }

            // create new operation
            $operation = new Operation();
            if (empty($operationPrefix)) {
                $operation->setOperationId($operationId);
            } else {
                $operation->setOperationId($operationPrefix . ucfirst($operationId));
            }

            if (!empty($description)) {
                $operation->setDescription($description);
            }

            // query parameter
            $queryParameters = $method->getQueryParameters();
            $parameters      = [];
            $properties      = $queryParameters->getProperties() ?: [];
            foreach ($properties as $name => $parameter) {
                $param = $this->getParameter($parameter, in_array($name, $queryParameters->getRequired() ?: []));
                $param->setName($name);
                $param->setIn('query');

                $parameters[] = $param;
            }

            // request body
            if ($request instanceof SchemaInterface) {
                $property = $request->getDefinition();

                $param = new Parameter();
                $param->setName('body');
                $param->setIn('body');
                $param->setDescription($property->getDescription() ?: $method->getName() . ' request');
                $param->setRequired(true);
                $param->setSchema((object) ['$ref' => '#/definitions/' . $this->getIdentifierForProperty($property)]);

                $parameters[] = $param;
            }

            $operation->setParameters($parameters);

            // response body
            $responses = $method->getResponses();
            $resps     = new Responses();

            foreach ($responses as $statusCode => $response) {
                /** @var \PSX\Schema\SchemaInterface $response */
                $property = $response->getDefinition();

                $resp = new Response();
                $resp->setDescription($property->getDescription() ?: $method->getName() . ' ' . $statusCode . ' response');
                $resp->setSchema((object) ['$ref' => '#/definitions/' . $this->getIdentifierForProperty($property)]);

                $resps["" . $statusCode] = $resp;
            }

            $operation->setResponses($resps);

            if ($method->getName() === 'GET') {
                $path->setGet($operation);
            } elseif ($method->getName() === 'POST') {
                $path->setPost($operation);
            } elseif ($method->getName() === 'PUT') {
                $path->setPut($operation);
            } elseif ($method->getName() === 'DELETE') {
                $path->setDelete($operation);
            } elseif ($method->getName() === 'PATCH') {
                $path->setPatch($operation);
            }
        }

        $paths[Inflection::transformRoutePlaceholder($resource->getPath())] = $path;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param \stdClass $definitions
     */
    protected function buildDefinitions(Resource $resource, \stdClass $definitions)
    {
        $generator  = new Generator\JsonSchema($this->targetNamespace);
        $properties = [];
        $methods    = $resource->getMethods();

        foreach ($methods as $name => $method) {
            // request
            $request = $method->getRequest();
            if ($request instanceof SchemaInterface) {
                $properties[$this->getIdentifierForProperty($request->getDefinition())] = $request;
            }

            // response
            $responses = $method->getResponses();
            foreach ($responses as $statusCode => $response) {
                if ($response instanceof SchemaInterface) {
                    $properties[$this->getIdentifierForProperty($response->getDefinition())] = $response;
                }
            }
        }

        foreach ($properties as $name => $property) {
            $schema = $generator->toArray($property);

            if (isset($schema['definitions'])) {
                foreach ($schema['definitions'] as $key => $definition) {
                    $definitions->{$key} = $definition;
                }

                unset($schema['definitions']);
            }

            if (isset($schema['$schema'])) {
                unset($schema['$schema']);
            }

            if (isset($schema['id'])) {
                unset($schema['id']);
            }

            $definitions->{$name} = $schema;
        }
    }

    /**
     * @param \PSX\Schema\PropertyInterface $parameter
     * @return \PSX\Model\Swagger\Parameter $param
     */
    protected function getParameter(PropertyInterface $parameter, $required)
    {
        $param = new Parameter();
        $param->setDescription($parameter->getDescription());
        $param->setRequired($required);
        $param->setType($parameter->getType());
        $param->setEnum($parameter->getEnum());

        $param->setFormat($parameter->getFormat());
        $param->setMinLength($parameter->getMinLength());
        $param->setMaxLength($parameter->getMaxLength());
        $param->setPattern($parameter->getPattern());

        $param->setMinimum($parameter->getMinimum());
        $param->setMaximum($parameter->getMaximum());
        $param->setMultipleOf($parameter->getMultipleOf());

        return $param;
    }
}
