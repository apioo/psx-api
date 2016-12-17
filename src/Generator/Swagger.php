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

namespace PSX\Api\Generator;

use PSX\Api\GeneratorAbstract;
use PSX\Api\Resource;
use PSX\Api\Util\Inflection;
use PSX\Data\ExporterInterface;
use PSX\Model\Swagger\Info;
use PSX\Model\Swagger\Items;
use PSX\Model\Swagger\Path;
use PSX\Model\Swagger\Paths;
use PSX\Model\Swagger\Response;
use PSX\Model\Swagger\Responses;
use PSX\Record\Record;
use PSX\Json\Parser;
use PSX\Model\Swagger\Swagger as Declaration;
use PSX\Model\Swagger\Model;
use PSX\Model\Swagger\Models;
use PSX\Model\Swagger\Operation;
use PSX\Model\Swagger\Parameter;
use PSX\Model\Swagger\Properties;
use PSX\Model\Swagger\ResponseMessage;
use PSX\Schema\Generator\GeneratorTrait;
use PSX\Schema\Generator;
use PSX\Schema\Property;
use PSX\Schema\PropertyInterface;
use PSX\Schema\SchemaInterface;

/**
 * Generates an Swagger 1.2 representation of an API resource. Note this does
 * not generate a resource listing only the documentation of an single resource
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Swagger extends GeneratorAbstract
{
    use GeneratorTrait;

    /**
     * @var \PSX\Data\ExporterInterface
     */
    protected $exporter;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $targetNamespace;

    /**
     * @param \PSX\Data\ExporterInterface $exporter
     * @param integer $apiVersion
     * @param string $basePath
     * @param string $targetNamespace
     */
    public function __construct(ExporterInterface $exporter, $apiVersion, $basePath, $targetNamespace)
    {
        $this->exporter        = $exporter;
        $this->apiVersion      = $apiVersion;
        $this->basePath        = $basePath;
        $this->targetNamespace = $targetNamespace;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    public function generate(Resource $resource)
    {
        $info = new Info();
        $info->setTitle('PSX');
        $info->setVersion($this->apiVersion);

        $swagger = new Declaration();
        $swagger->setInfo($info);
        $swagger->setBasePath($this->basePath);
        $swagger->setPaths($this->getPaths($resource));
        $swagger->setDefinitions($this->getDefinitions($resource));

        $swagger = $this->exporter->export($swagger);
        $swagger = Parser::encode($swagger, JSON_PRETTY_PRINT);

        return $swagger;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return \PSX\Model\Swagger\Paths
     */
    protected function getPaths(Resource $resource)
    {
        $paths = new Paths();
        $path  = new Path();

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
            $operationId = null;

            if ($request instanceof SchemaInterface) {
                $operationId = strtolower($method->getName()) . ucfirst($this->getIdentifierForProperty($request->getDefinition()));
            } elseif ($response instanceof SchemaInterface) {
                $operationId = strtolower($method->getName()) . ucfirst($this->getIdentifierForProperty($response->getDefinition()));
            }

            // create new operation
            $operation = new Operation();
            $operation->setOperationId($operationId);

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

        return $paths;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return \stdClass
     */
    protected function getDefinitions(Resource $resource)
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

        $definitions = new \stdClass();
        foreach ($properties as $name => $property) {
            $schema = $generator->toArray($property);

            if (isset($schema['definitions'])) {
                foreach ($schema['definitions'] as $definition) {
                    $definitions->{$name} = $definition;
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

        return $definitions;
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
