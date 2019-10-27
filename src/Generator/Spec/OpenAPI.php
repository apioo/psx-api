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

use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\Model\OpenAPI\Components;
use PSX\Model\OpenAPI\Contact;
use PSX\Model\OpenAPI\Info;
use PSX\Model\OpenAPI\License;
use PSX\Model\OpenAPI\MediaType;
use PSX\Model\OpenAPI\MediaTypes;
use PSX\Model\OpenAPI\OauthFlow;
use PSX\Model\OpenAPI\OauthFlows;
use PSX\Model\OpenAPI\OpenAPI as Declaration;
use PSX\Model\OpenAPI\Operation;
use PSX\Model\OpenAPI\Parameter;
use PSX\Model\OpenAPI\PathItem;
use PSX\Model\OpenAPI\Paths;
use PSX\Model\OpenAPI\RequestBody;
use PSX\Model\OpenAPI\Response;
use PSX\Model\OpenAPI\Responses;
use PSX\Model\OpenAPI\Scopes;
use PSX\Model\OpenAPI\SecurityRequirement;
use PSX\Model\OpenAPI\SecurityScheme;
use PSX\Model\OpenAPI\Server;
use PSX\Model\OpenAPI\Tag;
use PSX\Schema\Generator;
use PSX\Schema\Generator\GeneratorTrait;
use PSX\Schema\PropertyInterface;
use PSX\Schema\SchemaInterface;

/**
 * Generates an OpenAPI 3.0 representation of an API resource
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OpenAPI extends OpenAPIAbstract
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

        $schemas = $this->resolveRefs($schemas);

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

        $schemas = $this->resolveRefs($schemas);

        return $this->buildDeclaration($paths, $schemas);
    }

    /**
     * @param \PSX\Model\OpenAPI\Paths $paths
     * @param \stdClass $schemas
     * @return string
     */
    protected function buildDeclaration(Paths $paths, \stdClass $schemas)
    {
        $info = new Info();
        $info->setTitle($this->title ?: 'PSX');
        $info->setDescription($this->description);
        $info->setTermsOfService($this->tos);

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

        $info->setVersion($this->apiVersion);

        $server = new Server();
        $server->setUrl($this->baseUri);

        $components = new Components();
        $components->setSchemas($schemas);

        $this->buildSecuritySchemes($components);

        $openAPI = new Declaration();
        $openAPI->setInfo($info);
        $openAPI->setServers([$server]);
        $openAPI->setPaths($paths);
        $openAPI->setComponents($components);

        if (!empty($this->tags)) {
            $openAPI->setTags($this->tags);
        }

        $data = $this->dumper->dump($openAPI);
        $data = Parser::encode($data, JSON_PRETTY_PRINT);

        return $data;
    }

    /**
     * @param \PSX\Model\OpenAPI\Components $components
     */
    protected function buildSecuritySchemes(Components $components)
    {
        $schemes = [];
        foreach ($this->authFlows as $authName => $authFlows) {
            $flows = new OauthFlows();
            foreach ($authFlows as $authFlow) {
                list($flowType, $authorizationUrl, $tokenUrl, $refreshUrl, $scopes) = $authFlow;

                $flow = new OauthFlow();
                $flow->setAuthorizationUrl($authorizationUrl);
                $flow->setTokenUrl($tokenUrl);

                if (!empty($refreshUrl)) {
                    $flow->setRefreshUrl($refreshUrl);
                }

                if (!empty($scopes)) {
                    $flow->setScopes(new Scopes($scopes));
                }

                if ($flowType == self::FLOW_AUTHORIZATION_CODE) {
                    $flows->setAuthorizationCode($flow);
                } elseif ($flowType == self::FLOW_IMPLICIT) {
                    $flows->setImplicit($flow);
                } elseif ($flowType == self::FLOW_PASSWORD) {
                    $flows->setPassword($flow);
                } elseif ($flowType == self::FLOW_CLIENT_CREDENTIALS) {
                    $flows->setClientCredentials($flow);
                }
            }

            $scheme = new SecurityScheme();
            $scheme->setType('oauth2');
            $scheme->setFlows($flows);

            $schemes[$authName] = $scheme;
        }

        if (!empty($schemes)) {
            $components->setSecuritySchemes($schemes);
        }
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param \PSX\Model\OpenAPI\Paths $paths
     * @param string $operationPrefix
     */
    protected function buildPaths(Resource $resource, Paths $paths, $operationPrefix = null)
    {
        $path = new PathItem();

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

            // tags
            $tags = array_merge($resource->getTags(), $method->getTags());
            if (!empty($tags)) {
                $operation->setTags($tags);
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

            $security = $method->getSecurity();
            if (!empty($security)) {
                $operation->setSecurity([new SecurityRequirement($security)]);
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

    /**
     * @param \stdClass $schemas
     * @return \stdClass
     */
    private function resolveRefs(\stdClass $schemas)
    {
        // @TODO find a better way to replace the internal paths probably
        // provide the definition path to the json schema generator
        $json = json_encode($schemas);
        $json = str_replace('#\/definitions\/', '#\/components\/schemas\/', $json);

        return json_decode($json);
    }
}
