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

use PSX\Api\Operation\Argument;
use PSX\Api\OperationInterface;
use PSX\Api\OperationsInterface;
use PSX\Api\SpecificationInterface;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\OpenAPI\Components;
use PSX\OpenAPI\Contact;
use PSX\OpenAPI\Info;
use PSX\OpenAPI\License;
use PSX\OpenAPI\MediaType;
use PSX\OpenAPI\MediaTypes;
use PSX\OpenAPI\OAuthFlow;
use PSX\OpenAPI\OAuthFlows;
use PSX\OpenAPI\OpenAPI as Declaration;
use PSX\OpenAPI\Operation;
use PSX\OpenAPI\Parameter;
use PSX\OpenAPI\PathItem;
use PSX\OpenAPI\Paths;
use PSX\OpenAPI\RequestBody;
use PSX\OpenAPI\Response;
use PSX\OpenAPI\Responses;
use PSX\OpenAPI\Schemas;
use PSX\OpenAPI\Scopes;
use PSX\OpenAPI\SecurityRequirement;
use PSX\OpenAPI\SecurityScheme;
use PSX\OpenAPI\SecuritySchemes;
use PSX\OpenAPI\Server;
use PSX\OpenAPI\Tag;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\Parser\Popo\Dumper;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;

/**
 * Generates an OpenAPI 3.0 representation of an API resource
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OpenAPI extends ApiAbstract
{
    private int $apiVersion;
    private ?string $baseUri;

    private Dumper $dumper;
    private Generator\JsonSchema $generator;

    public function __construct(int $apiVersion, ?string $baseUri)
    {
        $this->apiVersion = $apiVersion;
        $this->baseUri    = $baseUri;
        $this->dumper     = new Dumper();
        $this->generator  = new Generator\JsonSchema('#/components/schemas/');
    }

    public function generate(SpecificationInterface $specification): Generator\Code\Chunks|string
    {
        $operations = $specification->getOperations();
        $definitions = $specification->getDefinitions();

        $paths = new Paths();
        $result = $this->groupOperationsByPath($operations);
        foreach ($result as $path => $operations) {
            $paths[Inflection::convertPlaceholderToCurly($path)] = $this->buildPathItem($operations, $definitions);
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

        $result = $this->generator->toArray(TypeFactory::getAny(), $definitions);

        $schemas = new Schemas();
        foreach ($result['definitions'] as $name => $schema) {
            $schemas[$name] = $schema;
        }

        $components = new Components();
        $components->setSchemas($schemas);

        $this->buildSecuritySchemes($components);

        $declaration = new Declaration();
        $declaration->setInfo($info);
        $declaration->setServers([$server]);
        $declaration->setPaths($paths);
        $declaration->setComponents($components);

        if (!empty($this->tags)) {
            $tags = [];
            foreach ($this->tags as $name => $description) {
                $tags[] = $this->newTag($name, $description);
            }
            $declaration->setTags($tags);
        }

        $data = $this->dumper->dump($declaration);
        $data = Parser::encode($data);

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

    protected function buildPathItem(array $operations, DefinitionsInterface $definitions): PathItem
    {
        $pathItem = new PathItem();
        $pathItem->setParameters($this->getPathParameters($operations, $definitions));

        foreach ($operations as $config) {
            [$method, $operationId, $operation] = $config;

            if ($method === 'GET') {
                $pathItem->setGet($this->getOperation($operationId, $operation, $definitions));
            } elseif ($method === 'POST') {
                $pathItem->setPost($this->getOperation($operationId, $operation, $definitions));
            } elseif ($method === 'PUT') {
                $pathItem->setPut($this->getOperation($operationId, $operation, $definitions));
            } elseif ($method === 'DELETE') {
                $pathItem->setDelete($this->getOperation($operationId, $operation, $definitions));
            } elseif ($method === 'PATCH') {
                $pathItem->setPatch($this->getOperation($operationId, $operation, $definitions));
            }
        }

        return $pathItem;
    }

    protected function newParameter(TypeInterface $type, bool $required, DefinitionsInterface $definitions): Parameter
    {
        $schema = $this->generator->toArray($type, $definitions);
        if (isset($schema['definitions'])) {
            unset($schema['definitions']);
        }

        $param = new Parameter();
        $param->setDescription($type->getDescription());
        $param->setRequired($required);
        $param->setSchema($schema);

        return $param;
    }

    protected function newTag(string $name, string $description): Tag
    {
        $tag = new Tag();
        $tag->setName($name);
        $tag->setDescription($description);

        return $tag;
    }

    private function getMediaTypes(TypeInterface $type, DefinitionsInterface $definitions): MediaTypes
    {
        $mediaType = new MediaType();

        if ($type instanceof ReferenceType) {
            $mediaType->setSchema((object) ['$ref' => '#/components/schemas/' . $type->getRef()]);
        } else {
            $mediaType->setSchema((object) $this->generator->toArray($type, $definitions));
        }

        $mediaTypes = new MediaTypes();
        $mediaTypes['application/json'] = $mediaType;

        return $mediaTypes;
    }

    private function groupOperationsByPath(OperationsInterface $operations): array
    {
        $result = [];
        foreach ($operations->getAll() as $operationId => $operation) {
            if (!isset($result[$operation->getPath()])) {
                $result[$operation->getPath()] = [];
            }

            $result[$operation->getPath()][] = [$operation->getMethod(), $operationId, $operation];
        }

        return $result;
    }

    private function getPathParameters(array $operations, DefinitionsInterface $definitions): array
    {
        $result = [];
        foreach ($operations as $config) {
            [$method, $operationId, $operation] = $config;

            $arguments = $operation->getArguments();
            foreach ($arguments->getAll() as $argumentName => $argument) {
                if ($argument->getIn() === Argument::IN_PATH) {
                    $param = $this->newParameter($argument->getSchema(), true, $definitions);
                    $param->setName($argumentName);
                    $param->setIn('path');
                    $result[$argumentName] = $param;
                }
            }
        }

        return array_values($result);
    }

    private function getQueryParameters(OperationInterface $operation, DefinitionsInterface $definitions): array
    {
        $result = [];
        $arguments = $operation->getArguments();
        foreach ($arguments->getAll() as $argumentName => $argument) {
            if ($argument->getIn() === Argument::IN_QUERY) {
                $param = $this->newParameter($argument->getSchema(), true, $definitions);
                $param->setName($argumentName);
                $param->setIn('query');
                $result[$argumentName] = $param;
            }
        }

        return array_values($result);
    }

    private function getBodyArgument(OperationInterface $operation): ?Argument
    {
        $arguments = $operation->getArguments();
        foreach ($arguments->getAll() as $argumentName => $argument) {
            if ($argument->getIn() === Argument::IN_BODY) {
                return $argument;
            }
        }

        return null;
    }

    private function getRequestBody(OperationInterface $operation, DefinitionsInterface $definitions): ?RequestBody
    {
        $argument = $this->getBodyArgument($operation);
        if (!$argument instanceof Argument) {
            return null;
        }

        $result = new RequestBody();
        //$result->setDescription('');
        $result->setContent($this->getMediaTypes($argument->getSchema(), $definitions));

        return $result;
    }

    private function getResponses(OperationInterface $operation, DefinitionsInterface $definitions): Responses
    {
        $result = new Responses();
        $result[strval($operation->getReturn()->getCode())] = $this->getResponse($operation->getReturn(), $definitions);

        foreach ($operation->getThrows() as $throw) {
            $result[strval($throw->getCode())] = $this->getResponse($throw, $definitions);
        }

        return $result;
    }

    private function getResponse(\PSX\Api\Operation\Response $response, DefinitionsInterface $definitions): Response
    {
        $result = new Response();
        $result->setDescription('');
        $result->setContent($this->getMediaTypes($response->getSchema(), $definitions));

        return $result;
    }

    private function getOperation(string $operationId, OperationInterface $operation, DefinitionsInterface $definitions): Operation
    {
        $result = new Operation();
        $result->setOperationId($operationId);

        $description = $operation->getDescription();
        if (!empty($description)) {
            $result->setDescription($description);
        }

        $queryParameters = $this->getQueryParameters($operation, $definitions);
        if (!empty($queryParameters)) {
            $result->setParameters($queryParameters);
        }

        $requestBody = $this->getRequestBody($operation, $definitions);
        if ($requestBody instanceof RequestBody) {
            $result->setRequestBody($requestBody);
        }

        $responses = $this->getResponses($operation, $definitions);
        if (!empty($responses)) {
            $result->setResponses($responses);
        }

        $security = $operation->getSecurity();
        if (!empty($security)) {
            $authName = array_key_first($this->authFlows);
            if (!empty($authName)) {
                $result->setSecurity([SecurityRequirement::fromArray([$authName => $security])]);
            }
        }

        $tags = $operation->getTags();
        if (!empty($tags)) {
            $result->setTags($tags);
        }

        if ($operation->getStability() === OperationInterface::STABILITY_DEPRECATED) {
            $result->setDeprecated(true);
        }

        return $result;
    }
}
