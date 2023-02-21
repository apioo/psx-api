<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\SpecificationInterface;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\Model\OpenAPI\Components;
use PSX\Model\OpenAPI\Contact;
use PSX\Model\OpenAPI\Info;
use PSX\Model\OpenAPI\License;
use PSX\Model\OpenAPI\MediaType;
use PSX\Model\OpenAPI\MediaTypes;
use PSX\Model\OpenAPI\OAuthFlow;
use PSX\Model\OpenAPI\OAuthFlows;
use PSX\Model\OpenAPI\OpenAPI as Declaration;
use PSX\Model\OpenAPI\Operation;
use PSX\Model\OpenAPI\Parameter;
use PSX\Model\OpenAPI\PathItem;
use PSX\Model\OpenAPI\Paths;
use PSX\Model\OpenAPI\RequestBody;
use PSX\Model\OpenAPI\Response;
use PSX\Model\OpenAPI\Responses;
use PSX\Model\OpenAPI\Schemas;
use PSX\Model\OpenAPI\Scopes;
use PSX\Model\OpenAPI\SecurityRequirement;
use PSX\Model\OpenAPI\SecurityScheme;
use PSX\Model\OpenAPI\SecuritySchemes;
use PSX\Model\OpenAPI\Server;
use PSX\Model\OpenAPI\Tag;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\Type\StructType;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;

/**
 * Generates an OpenAPI 3.0 representation of an API resource
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OpenAPI extends OpenAPIAbstract
{
    public function generate(SpecificationInterface $specification): Generator\Code\Chunks|string
    {
        $collection = $specification->getResourceCollection();
        $definitions = $specification->getDefinitions();

        $removeTypes = [];
        $paths = new Paths();
        foreach ($collection as $path => $resource) {
            $this->buildPaths($resource, $paths, $definitions, $removeTypes);
        }

        // the definitions contain types for path and query parameters which we can remove
        foreach ($removeTypes as $type) {
            $definitions->removeType($type);
        }

        return $this->buildDeclaration($paths, $definitions);
    }

    protected function buildDeclaration(Paths $paths, DefinitionsInterface $definitions): string
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

        $generator = new Generator\JsonSchema('#/components/schemas/');
        $result    = $generator->toArray(TypeFactory::getAny(), $definitions);

        $schemas = new Schemas();
        foreach ($result['definitions'] as $name => $schema) {
            $schemas[$name] = $schema;
        }

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

    protected function buildSecuritySchemes(Components $components): void
    {
        $schemes = new SecuritySchemes();
        foreach ($this->authFlows as $authName => $authFlows) {
            $flows = new OAuthFlows();
            foreach ($authFlows as $authFlow) {
                [$flowType, $authorizationUrl, $tokenUrl, $refreshUrl, $scopes] = $authFlow;

                $flow = new OAuthFlow();
                $flow->setAuthorizationUrl($authorizationUrl);
                $flow->setTokenUrl($tokenUrl);

                if (!empty($refreshUrl)) {
                    $flow->setRefreshUrl($refreshUrl);
                }

                if (!empty($scopes)) {
                    $result = new Scopes();
                    foreach ($scopes as $name => $title) {
                        $result[$name] = $title;
                    }
                    $flow->setScopes($result);
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

        if (count($schemes->getProperties()) > 0) {
            $components->setSecuritySchemes($schemes);
        }
    }

    protected function buildPaths(Resource $resource, Paths $paths, DefinitionsInterface $definitions, array &$removeTypes): void
    {
        $path = new PathItem();

        // path parameter
        $pathParameters = $resource->getPathParameters();
        if (!empty($pathParameters) && $definitions->hasType($pathParameters)) {
            $parameters = $this->newParameters($definitions->getType($pathParameters), 'path', $definitions);
            if (!empty($parameters)) {
                $path->setParameters($parameters);
                $removeTypes[] = $pathParameters;
            }
        }

        $methods = $resource->getMethods();
        foreach ($methods as $method) {
            $operation = new Operation();

            // operation
            $operationId = $method->getOperationId();
            if (!empty($operationId)) {
                $operation->setOperationId($operationId);
            }

            // description
            $description = $method->getDescription();
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
            if (!empty($queryParameters) && $definitions->hasType($queryParameters)) {
                $parameters = $this->newParameters($definitions->getType($queryParameters), 'query', $definitions);
                if (!empty($parameters)) {
                    $operation->setParameters($parameters);
                    $removeTypes[] = $queryParameters;
                }
            }

            // request body
            $request = $method->getRequest();
            if (!empty($request)) {
                $requestBody = new RequestBody();
                $requestBody->setDescription($method->getName() . ' Request');
                $requestBody->setContent($this->getMediaTypes($request));

                $operation->setRequestBody($requestBody);
            }

            // response body
            $responses = $method->getResponses();
            $resps     = new Responses();

            foreach ($responses as $statusCode => $response) {
                $resp = new Response();
                $resp->setDescription($method->getName() . ' ' . $statusCode . ' Response');
                $resp->setContent($this->getMediaTypes($response));

                $resps[strval($statusCode)] = $resp;
            }

            $operation->setResponses($resps);

            // security
            $security = $method->getSecurity();
            if (!empty($security)) {
                $operation->setSecurity([SecurityRequirement::fromArray($security)]);
            }

            // tags
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

        $paths[Inflection::convertPlaceholderToCurly($resource->getPath())] = $path;
    }

    private function newParameters(TypeInterface $type, string $in, DefinitionsInterface $definitions): array
    {
        if (!$type instanceof StructType) {
            return [];
        }

        $parameters = [];
        if ($type->getExtends() && $definitions->hasType($type->getExtends())) {
            $parameters = $this->newParameters($definitions->getType($type->getExtends()), $in, $definitions);
        }

        $properties = $type->getProperties();
        if ($properties) {
            foreach ($properties as $name => $parameter) {
                $param = $this->newParameter($parameter, in_array($name, $type->getRequired() ?: []));
                $param->setName($name);
                $param->setIn($in);

                $parameters[] = $param;
            }
        }

        return $parameters;
    }

    protected function newParameter(TypeInterface $type, bool $required): Parameter
    {
        $param = new Parameter();
        $param->setDescription($type->getDescription());
        $param->setRequired($required);
        $param->setSchema($type->toArray());

        return $param;
    }

    protected function newTag(string $name, string $description): Tag
    {
        $tag = new Tag();
        $tag->setName($name);
        $tag->setDescription($description);

        return $tag;
    }

    private function getMediaTypes(string $type): MediaTypes
    {
        $mediaType = new MediaType();
        $mediaType->setSchema((object) ['$ref' => '#/components/schemas/' . $type]);

        $mediaTypes = new MediaTypes();
        $mediaTypes['application/json'] = $mediaType;

        return $mediaTypes;
    }
}
