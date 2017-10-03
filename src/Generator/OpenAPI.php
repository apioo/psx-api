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
use PSX\Api\Resource;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\Model\OpenAPI\Components;
use PSX\Model\OpenAPI\Info;
use PSX\Model\OpenAPI\MediaType;
use PSX\Model\OpenAPI\MediaTypes;
use PSX\Model\OpenAPI\OpenAPI as Declaration;
use PSX\Model\OpenAPI\Operation;
use PSX\Model\OpenAPI\Parameter;
use PSX\Model\OpenAPI\PathItem;
use PSX\Model\OpenAPI\Paths;
use PSX\Model\OpenAPI\RequestBody;
use PSX\Model\OpenAPI\Response;
use PSX\Model\OpenAPI\Responses;
use PSX\Model\OpenAPI\Server;
use PSX\Schema\Generator;
use PSX\Schema\Generator\GeneratorTrait;
use PSX\Schema\Parser\Popo\Dumper;
use PSX\Schema\PropertyInterface;
use PSX\Schema\SchemaInterface;

/**
 * Generates an OpenAPI 3.0 representation of an API resource
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OpenAPI extends GeneratorAbstract
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
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    public function generate(Resource $resource)
    {
        $info = new Info();
        $info->setTitle('PSX');
        $info->setVersion($this->apiVersion);

        $server = new Server();
        $server->setUrl($this->baseUri);

        $components = new Components();
        $components->setSchemas($this->getDefinitions($resource));

        $openAPI = new Declaration();
        $openAPI->setInfo($info);
        $openAPI->setServers([$server]);
        $openAPI->setPaths($this->getPaths($resource));
        $openAPI->setComponents($components);

        $data = $this->dumper->dump($openAPI);
        $data = Parser::encode($data, JSON_PRETTY_PRINT);

        return $data;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return \PSX\Model\OpenAPI\Paths
     */
    protected function getPaths(Resource $resource)
    {
        $paths = new Paths();
        $path  = new PathItem();

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

            if (!empty($parameters)) {
                $operation->setParameters($parameters);
            }

            // request body
            if ($request instanceof SchemaInterface) {
                $property = $request->getDefinition();

                $mediaType = new MediaType();
                $mediaType->setSchema((object) ['$ref' => '#/components/schemas/' . $this->getIdentifierForProperty($property)]);

                $mediaTypes = new MediaTypes();
                $mediaTypes->set('application/json', $mediaType);

                $requestBody = new RequestBody();
                $requestBody->setContent($mediaTypes);

                $operation->setRequestBody($requestBody);
            }

            // response body
            $responses = $method->getResponses();
            $resps     = new Responses();

            foreach ($responses as $statusCode => $response) {
                /** @var \PSX\Schema\SchemaInterface $response */
                $property = $response->getDefinition();

                $mediaType = new MediaType();
                $mediaType->setSchema((object) ['$ref' => '#/components/schemas/' . $this->getIdentifierForProperty($property)]);

                $mediaTypes = new MediaTypes();
                $mediaTypes->set('application/json', $mediaType);

                $resp = new Response();
                $resp->setDescription($property->getDescription() ?: $method->getName() . ' ' . $statusCode . ' response');
                $resp->setContent($mediaTypes);

                $resps->set(strval($statusCode), $resp);
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

        // @TODO find a better way to replace the internal paths probably
        // provide the definition path to the json schema generator
        $json = json_encode($definitions);
        $json = str_replace('#\/definitions\/', '#\/components\/schemas\/', $json);

        return json_decode($json);
    }

    /**
     * @param \PSX\Schema\PropertyInterface $parameter
     * @return \PSX\Model\OpenAPI\Parameter $param
     */
    protected function getParameter(PropertyInterface $parameter, $required)
    {
        $param = new Parameter();
        $param->setDescription($parameter->getDescription());
        $param->setRequired($required);
        $param->setSchema($parameter->toArray());

        return $param;
    }
}
