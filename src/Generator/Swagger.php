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

namespace PSX\Api\Generator;

use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\Model\Swagger\Contact;
use PSX\Model\Swagger\Info;
use PSX\Model\Swagger\License;
use PSX\Model\Swagger\Operation;
use PSX\Model\Swagger\Parameter;
use PSX\Model\Swagger\Path;
use PSX\Model\Swagger\Paths;
use PSX\Model\Swagger\Response;
use PSX\Model\Swagger\Responses;
use PSX\Model\Swagger\Scopes;
use PSX\Model\Swagger\Security;
use PSX\Model\Swagger\SecurityDefinitions;
use PSX\Model\Swagger\SecurityScheme;
use PSX\Model\Swagger\Swagger as Declaration;
use PSX\Model\Swagger\Tag;
use PSX\Schema\Generator;
use PSX\Schema\Generator\GeneratorTrait;
use PSX\Schema\PropertyInterface;
use PSX\Schema\SchemaInterface;

/**
 * Generates a Swagger 2.0 representation of an API resource
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Swagger extends OpenAPIAbstract
{
    use GeneratorTrait;

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

        if (!empty($this->contactName)) {
            $contact = new Contact();
            $contact->setName($this->contactName);
            $contact->setUrl($this->contactUrl);
            $contact->setEmail($this->contactEmail);

            $info->setContact($contact);
        }

        if (!empty($this->licenseName)) {
            $license = new License();
            $license->setName($this->licenseName);
            $license->setUrl($this->licenseUrl);

            $info->setLicense($license);
        }

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

        if (!empty($this->tags)) {
            $swagger->setTags($this->tags);
        }

        $this->buildSecuritySchemes($swagger);

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

            // security
            $security = $method->getSecurity();
            if (!empty($security)) {
                $operation->setSecurity([new Security($security)]);
            }

            $tags = $method->getTags();
            if (!empty($tags)) {
                $operation->setTags($tags);
            }

            if ($resource->getStatus() == Resource::STATUS_DEPRECATED) {
                $operation->setDeprecated(true);
            }

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
     * @param \PSX\Model\Swagger\Swagger $swagger
     */
    protected function buildSecuritySchemes(\PSX\Model\Swagger\Swagger $swagger)
    {
        $flows = new SecurityDefinitions();

        foreach ($this->authFlows as $authName => $authFlows) {
            foreach ($authFlows as $authFlow) {
                list($flowType, $authorizationUrl, $tokenUrl, $refreshUrl, $scopes) = $authFlow;

                $flow = new SecurityScheme();
                $flow->setType('oauth2');

                if ($flowType == self::FLOW_AUTHORIZATION_CODE) {
                    $flow->setFlow('accessCode');
                    $flow->setAuthorizationUrl($authorizationUrl);
                    $flow->setTokenUrl($tokenUrl);
                } elseif ($flowType == self::FLOW_IMPLICIT) {
                    $flow->setFlow('implicit');
                    $flow->setAuthorizationUrl($authorizationUrl);
                } elseif ($flowType == self::FLOW_PASSWORD) {
                    $flow->setFlow('password');
                    $flow->setTokenUrl($tokenUrl);
                } elseif ($flowType == self::FLOW_CLIENT_CREDENTIALS) {
                    $flow->setFlow('application');
                    $flow->setTokenUrl($tokenUrl);
                }

                if (!empty($scopes)) {
                    $flow->setScopes(new Scopes($scopes));
                }
            }

            $flows[$authName] = $flow;
        }

        if (count($flows) > 0) {
            $swagger->setSecurityDefinitions($flows);
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

    /**
     * @inheritdoc
     */
    protected function newTag($name, $description)
    {
        $tag = new Tag();
        $tag->setName($name);
        $tag->setDescription($description);

        return $tag;
    }
}
