<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Parser;

use PSX\Api\ParserInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\Security\ApiKey;
use PSX\Api\Security\AuthorizationCode;
use PSX\Api\Security\ClientCredentials;
use PSX\Api\Security\HttpBasic;
use PSX\Api\Security\HttpBearer;
use PSX\Api\SecurityInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\Model\OpenAPI\Components;
use PSX\Model\OpenAPI\MediaType;
use PSX\Model\OpenAPI\MediaTypes;
use PSX\Model\OpenAPI\OAuthFlow;
use PSX\Model\OpenAPI\OpenAPI as OpenAPIModel;
use PSX\Model\OpenAPI\Operation;
use PSX\Model\OpenAPI\Parameter;
use PSX\Model\OpenAPI\PathItem;
use PSX\Model\OpenAPI\Reference;
use PSX\Model\OpenAPI\RequestBody;
use PSX\Model\OpenAPI\Response;
use PSX\Model\OpenAPI\Responses;
use PSX\Model\OpenAPI\SecurityScheme;
use PSX\Model\OpenAPI\SecuritySchemes;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Exception\TypeNotFoundException;
use PSX\Schema\Parser as SchemaParser;
use PSX\Schema\SchemaTraverser;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\Type\StructType;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;
use PSX\Schema\Visitor\TypeVisitor;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * OpenAPI
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OpenAPI implements ParserInterface
{
    private ?string $basePath;
    private SchemaParser\TypeSchema $schemaParser;
    private ?DefinitionsInterface $definitions = null;
    private ?\PSX\Model\OpenAPI\OpenAPI $document = null;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath;
        $this->schemaParser = new SchemaParser\TypeSchema(null, $basePath);
    }

    /**
     * @inheritdoc
     */
    public function parse(string $schema, ?string $path = null): SpecificationInterface
    {
        $this->parseOpenAPI($schema);

        $collection = new ResourceCollection();

        if ($path !== null) {
            $path = Inflection::convertPlaceholderToCurly($path);
        }

        $paths = $this->document->getPaths();
        foreach ($paths as $key => $spec) {
            if ($path !== null && $path !== $key) {
                continue;
            }

            $resource = $this->parseResource($spec, Inflection::convertPlaceholderToColon($key));
            $collection->set($resource);
        }

        return new Specification(
            $collection,
            $this->definitions,
            $this->parseSecurity()
        );
    }

    private function parseResource(PathItem $data, string $path): Resource
    {
        $status   = Resource::STATUS_ACTIVE;
        $resource = new Resource($status, $path);
        $typePrefix = Inflection::generateTitleFromRoute($path);

        $resource->setDescription($data->getDescription());

        $this->parseUriParameters($resource, $data, $typePrefix);

        $methods = [
            'get' => $data->getGet(),
            'post' => $data->getPost(),
            'put' => $data->getPut(),
            'delete' => $data->getDelete(),
            'patch' => $data->getPatch(),
        ];

        foreach ($methods as $methodName => $operation) {
            if (!$operation instanceof Operation) {
                continue;
            }

            $method = Resource\Factory::getMethod(strtoupper($methodName));

            $method->setOperationId($operation->getOperationId());
            $method->setDescription($operation->getSummary());
            $method->setTags($operation->getTags() ?? []);

            $this->parseQueryParameters($method, $operation, $typePrefix);
            $this->parseRequest($method, $operation->getRequestBody(), $typePrefix);
            $this->parseResponses($method, $operation, $typePrefix);

            $resource->addMethod($method);
        }

        return $resource;
    }

    /**
     * Currently we only use one security object for the complete API since this simplifies the usage of the generated
     * client since a user has not to configure multiple ways, in most cases an API has also only one way to authenticate.
     *
     * @return SecurityInterface|null
     */
    private function parseSecurity(): ?SecurityInterface
    {
        $components = $this->document->getComponents();
        if (!$components instanceof Components) {
            return null;
        }

        $securitySchemas = $components->getSecuritySchemes();
        if (!$securitySchemas instanceof SecuritySchemes) {
            return null;
        }

        foreach ($securitySchemas as $securityObject) {
            if (!$securityObject instanceof SecurityScheme) {
                continue;
            }

            if (strtolower($securityObject->getType()) === 'http' && strtolower($securityObject->getScheme()) === 'basic') {
                return new HttpBasic();
            } elseif (strtolower($securityObject->getType()) === 'http' && strtolower($securityObject->getScheme()) === 'bearer') {
                return new HttpBearer();
            } elseif (strtolower($securityObject->getType()) === 'apikey') {
                return new ApiKey($securityObject->getName(), $securityObject->getIn());
            } elseif (strtolower($securityObject->getType()) === 'oauth2') {
                $flows = $securityObject->getFlows();
                $clientCredentials = $flows->getClientCredentials();
                $authorizationCode = $flows->getAuthorizationCode();
                if ($clientCredentials instanceof OAuthFlow) {
                    return new ClientCredentials($clientCredentials->getTokenUrl(), $clientCredentials->getAuthorizationUrl(), $clientCredentials->getRefreshUrl());
                } elseif ($authorizationCode instanceof OAuthFlow) {
                    return new AuthorizationCode($authorizationCode->getTokenUrl(), $authorizationCode->getAuthorizationUrl(), $authorizationCode->getRefreshUrl());
                }
            }
        }

        return null;
    }

    /**
     * @param Resource $resource
     * @param PathItem $data
     * @param string $typePrefix
     * @throws TypeNotFoundException
     */
    private function parseUriParameters(Resource $resource, PathItem $data, string $typePrefix)
    {
        $type = $this->parseParameters('path', $data->getParameters() ?? []);
        if (!$type instanceof StructType) {
            return;
        }

        $typeName = $typePrefix . 'Path';
        $this->definitions->addType($typeName, $type);

        $resource->setPathParameters($typeName);
    }

    /**
     * @param Resource\MethodAbstract $method
     * @param Operation $data
     * @param string $typePrefix
     * @throws TypeNotFoundException
     */
    private function parseQueryParameters(Resource\MethodAbstract $method, Operation $data, string $typePrefix)
    {
        $type = $this->parseParameters('query', $data->getParameters() ?? []);
        if (!$type instanceof StructType) {
            return;
        }

        $typeName = $typePrefix . ucfirst(strtolower($method->getName())) . 'Query';
        $this->definitions->addType($typeName, $type);

        $method->setQueryParameters($typeName);
    }

    /**
     * @param string $type
     * @param array $data
     * @return StructType
     * @throws TypeNotFoundException
     */
    private function parseParameters(string $type, array $data): ?StructType
    {
        $return = TypeFactory::getStruct();
        $required = [];

        foreach ($data as $index => $definition) {
            [$name, $property, $isRequired] = $this->parseParameter($type, $definition);

            if ($name !== null) {
                if ($property instanceof TypeInterface) {
                    $return->addProperty($name, $property);
                }

                if ($isRequired !== null && $isRequired === true) {
                    $required[] = $name;
                }
            }
        }

        if (!$return->getProperties()) {
            return null;
        }

        $return->setRequired($required);

        return $return;
    }

    /**
     * @param string $in
     * @param Parameter|Reference $data
     * @return array|TypeInterface
     * @throws TypeNotFoundException
     */
    private function parseParameter(string $in, $data)
    {
        if ($data instanceof Reference) {
            return $this->parseParameter($in, $this->resolveReference($data->getRef()));
        }

        if (!$data instanceof Parameter) {
            throw new \RuntimeException('Not a parameter provided');
        }

        $name = $data->getName();
        $type = TypeFactory::getString();

        $property = null;
        $required = null;
        if (!empty($name) && $data->getIn() == $in) {
            $required = $data->getRequired() ?? false;

            $schema = $data->getSchema();
            if ($schema instanceof \stdClass) {
                $type = $this->schemaParser->parseType($schema);
                if ($type instanceof ReferenceType) {
                    $type = $this->definitions->getType($type->getRef());
                }
            }
        }

        return [
            $name,
            $type,
            $required
        ];
    }

    private function parseRequest(Resource\MethodAbstract $method, $requestBody, string $typePrefix)
    {
        if ($requestBody instanceof Reference) {
            $this->parseRequest($method, $this->resolveReference($requestBody->getRef()), $typePrefix);
        } elseif ($requestBody instanceof RequestBody) {
            $mediaTypes = $requestBody->getContent();
            if ($mediaTypes instanceof MediaTypes) {
                $schema = $this->getSchemaFromMediaTypes($mediaTypes, $typePrefix . ucfirst(strtolower($method->getName())) . 'Request');
                if (!empty($schema)) {
                    $method->setRequest($schema);
                }
            }
        }
    }

    private function parseResponses(Resource\MethodAbstract $method, Operation $operation, string $typePrefix)
    {
        $responses = $operation->getResponses();
        if ($responses instanceof Responses) {
            foreach ($responses as $statusCode => $response) {
                /** @var Response $response */
                $statusCode = (int) $statusCode;
                if ($statusCode < 100) {
                    continue;
                }

                $mediaTypes = $response->getContent();
                if ($mediaTypes instanceof MediaTypes) {
                    $schema = $this->getSchemaFromMediaTypes($mediaTypes, $typePrefix . ucfirst(strtolower($method->getName())) . $statusCode . 'Response');
                    if (!empty($schema)) {
                        $method->addResponse($statusCode, $schema);
                    }
                }
            }
        }
    }

    private function getSchemaFromMediaTypes(MediaTypes $mediaTypes, string $typeName): ?string
    {
        $mediaType = $mediaTypes['application/json'] ?? null;
        if (!$mediaType instanceof MediaType) {
            return null;
        }

        $schema = $mediaType->getSchema();
        if (!$schema instanceof \stdClass) {
            return null;
        }

        $type = $this->schemaParser->parseType($schema);
        if ($type instanceof ReferenceType) {
            return $type->getRef();
        }

        $this->definitions->addType($typeName, $type);

        return $typeName;
    }

    private function resolveReference(string $reference)
    {
        $parts = explode('/', $reference);
        $type = $parts[2] ?? null;
        $name = $parts[3] ?? null;
        if ($type === 'schemas') {
            return $this->definitions->getType($name);
        } elseif ($type === 'parameters') {
            return $this->document->getComponents()->getParameters()->getProperty($name);
        } elseif ($type === 'requestBodies') {
            return $this->document->getComponents()->getRequestBodies()->getProperty($name);
        } elseif ($type === 'responses') {
            return $this->document->getComponents()->getResponses()->getProperty($name);
        } elseif ($type === 'headers') {
            return $this->document->getComponents()->getHeaders()->getProperty($name);
        } elseif ($type === 'examples') {
            return $this->document->getComponents()->getExamples()->getProperty($name);
        } elseif ($type === 'links') {
            return $this->document->getComponents()->getLinks()->getProperty($name);
        } elseif ($type === 'callbacks') {
            return $this->document->getComponents()->getCallbacks()->getProperty($name);
        } else {
            throw new \RuntimeException('Could not resolve reference ' . $reference);
        }
    }

    private function parseOpenAPI(string $data): void
    {
        $data = Parser::decode($data);

        // create a schema based on the open API models
        $parser = new SchemaParser\Popo();
        $schema = $parser->parse(OpenAPIModel::class);

        $this->definitions = $this->schemaParser->parseSchema($data)->getDefinitions();
        $this->document    = (new SchemaTraverser())->traverse($data, $schema, new TypeVisitor());
    }

    public static function fromFile(string $file, ?string $path = null): SpecificationInterface
    {
        if (empty($file) || !is_file($file)) {
            throw new RuntimeException('Could not load OpenAPI schema ' . $file);
        }

        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array($extension, ['yaml', 'yml'])) {
            $data = json_encode(Yaml::parse(file_get_contents($file)));
        } else {
            $data = file_get_contents($file);
        }

        $basePath = pathinfo($file, PATHINFO_DIRNAME);
        $parser   = new OpenAPI($basePath);

        return $parser->parse($data, $path);
    }
}
