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

namespace PSX\Api\Parser;

use PSX\Api\Exception\InvalidArgumentException;
use PSX\Api\Exception\ParserException;
use PSX\Api\Operation;
use PSX\Api\Operation\Argument;
use PSX\Api\Operation\Arguments;
use PSX\Api\Operation\Response;
use PSX\Api\Operations;
use PSX\Api\OperationsInterface;
use PSX\Api\ParserInterface;
use PSX\Api\Security\ApiKey;
use PSX\Api\Security\HttpBasic;
use PSX\Api\Security\HttpBearer;
use PSX\Api\Security\OAuth2;
use PSX\Api\SecurityInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\OpenAPI\Components;
use PSX\OpenAPI\MediaType;
use PSX\OpenAPI\MediaTypes;
use PSX\OpenAPI\OAuthFlow;
use PSX\OpenAPI\OpenAPI as OpenAPIModel;
use PSX\OpenAPI\Operation as OpenAPIOperation;
use PSX\OpenAPI\Parameter;
use PSX\OpenAPI\PathItem;
use PSX\OpenAPI\Reference;
use PSX\OpenAPI\RequestBody;
use PSX\OpenAPI\Responses;
use PSX\OpenAPI\SecurityScheme;
use PSX\OpenAPI\SecuritySchemes;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Exception\InvalidSchemaException;
use PSX\Schema\Exception\TypeNotFoundException;
use PSX\Schema\Inspector\Hash;
use PSX\Schema\Parser as SchemaParser;
use PSX\Schema\SchemaInterface;
use PSX\Schema\SchemaTraverser;
use PSX\Schema\Type\ArrayType;
use PSX\Schema\Type\IntersectionType;
use PSX\Schema\Type\MapType;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\Type\StructType;
use PSX\Schema\Type\UnionType;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;
use PSX\Schema\Visitor\TypeVisitor;
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
    private Hash $hashInspector;
    private ?DefinitionsInterface $definitions = null;
    private ?OpenAPIModel $document = null;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath;
        $this->schemaParser = new SchemaParser\TypeSchema(null, $basePath);
        $this->hashInspector = new Hash();
    }

    /**
     * @throws ParserException
     */
    public function parse(string $schema): SpecificationInterface
    {
        try {
            $data = Parser::decode($schema);
            if (!$data instanceof \stdClass) {
                throw new ParserException('Provided schema must be an object');
            }

            return $this->parseObject($data);
        } catch (\JsonException $e) {
            throw new ParserException('Could not parse JSON: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws ParserException
     */
    public function parseObject(\stdClass $data): SpecificationInterface
    {
        try {
            $this->definitions = $this->schemaParser->parseSchema($data)->getDefinitions();
            $this->document    = (new SchemaTraverser())->traverse($data, $this->getSchema(), new TypeVisitor());

            $operations = new Operations();

            $paths = $this->document->getPaths();
            foreach ($paths as $key => $spec) {
                $this->parseResource($spec, Inflection::convertPlaceholderToColon($key), $operations);
            }

            return new Specification(
                $operations,
                $this->definitions,
                $this->parseSecurity()
            );
        } catch (\Throwable $e) {
            throw new ParserException('An error occurred while parsing: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @throws TypeNotFoundException
     * @throws InvalidSchemaException
     */
    private function parseResource(PathItem $data, string $path, OperationsInterface $operations): void
    {
        $methods = [
            'get' => $data->getGet(),
            'post' => $data->getPost(),
            'put' => $data->getPut(),
            'delete' => $data->getDelete(),
            'patch' => $data->getPatch(),
        ];

        $pathArguments = $this->parseUriParameters($data);

        foreach ($methods as $methodName => $operation) {
            if (!$operation instanceof OpenAPIOperation) {
                continue;
            }

            $responses = $this->parseResponseInRange($operation, 200, 300);
            $return = $responses[0] ?? null;
            if (!$return instanceof Response) {
                $return = new Response(204, TypeFactory::getAny());
            }

            $arguments = $pathArguments->withAdded($this->parseQueryParameters($operation));

            $request = $this->parseRequest($operation->getRequestBody());
            if ($request instanceof Argument) {
                $arguments->add('payload', $request);
            }

            $responses = $this->parseResponseInRange($operation, 400, 600);

            $result = new Operation(strtoupper($methodName), $path, $return);
            $result->setArguments($arguments);
            $result->setThrows($responses);

            if ($operation->getSummary() !== null) {
                $result->setDescription($operation->getSummary());
            }

            if ($operation->getDeprecated() !== null) {
                $result->setStability($operation->getDeprecated());
            }

            if ($operation->getSecurity() !== null) {
                $scopes = $this->getFirstSecurityScopes($operation->getSecurity());
                if ($scopes !== null) {
                    $result->setSecurity($scopes);
                }
            }

            if ($operation->getTags() !== null) {
                $result->setTags($operation->getTags());
            }

            $operationId = $operation->getOperationId();
            if (empty($operationId)) {
                $operationId = '';
            }

            $operations->add($operationId, $result);
        }
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
                    return new OAuth2($clientCredentials->getTokenUrl(), $clientCredentials->getAuthorizationUrl());
                } elseif ($authorizationCode instanceof OAuthFlow) {
                    return new OAuth2($authorizationCode->getTokenUrl(), $authorizationCode->getAuthorizationUrl());
                }
            }
        }

        return null;
    }

    /**
     * @throws InvalidSchemaException
     * @throws TypeNotFoundException
     */
    private function parseUriParameters(PathItem $data): Arguments
    {
        return $this->parseParameters('path', $data->getParameters() ?? []);
    }

    /**
     * @throws InvalidSchemaException
     * @throws TypeNotFoundException
     */
    private function parseQueryParameters(OpenAPIOperation $data): Arguments
    {
        return $this->parseParameters('query', $data->getParameters() ?? []);
    }

    /**
     * @throws TypeNotFoundException
     * @throws InvalidSchemaException
     */
    private function parseParameters(string $type, array $data): Arguments
    {
        $return = new Arguments();
        foreach ($data as $definition) {
            [$name, $property, $isRequired] = $this->parseParameter($type, $definition);

            if ($name !== null) {
                if ($property instanceof TypeInterface) {
                    $return->add($name, new Argument($type, $property));
                }
            }
        }

        return $return;
    }

    /**
     * @throws TypeNotFoundException
     * @throws InvalidSchemaException
     */
    private function parseParameter(string $in, Parameter|Reference $data): array
    {
        if ($data instanceof Reference) {
            return $this->parseParameter($in, $this->resolveReference($data->getRef()));
        }

        if (!$data instanceof Parameter) {
            throw new \RuntimeException('Not a parameter provided');
        }

        $name = $data->getName();
        $type = TypeFactory::getString();

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

    /**
     * @throws InvalidSchemaException
     */
    private function parseRequest($requestBody): ?Argument
    {
        if ($requestBody instanceof Reference) {
            return $this->parseRequest($this->resolveReference($requestBody->getRef()));
        } elseif ($requestBody instanceof RequestBody) {
            $mediaTypes = $requestBody->getContent();
            if ($mediaTypes instanceof MediaTypes) {
                $schema = $this->getSchemaFromMediaTypes($mediaTypes);
                if (!empty($schema)) {
                    return new Argument('body', $schema);
                }
            }
        }

        return null;
    }

    /**
     * @throws InvalidSchemaException
     */
    private function parseResponseInRange(OpenAPIOperation $operation, int $start, int $end): array
    {
        $result = [];
        $responses = $operation->getResponses();
        if (!$responses instanceof Responses) {
            return $result;
        }

        foreach ($responses as $statusCode => $response) {
            /** @var \PSX\OpenAPI\Response $response */
            $statusCode = (int) $statusCode;
            if ($statusCode >= $start && $statusCode < $end) {
                $mediaTypes = $response->getContent();
                if ($mediaTypes instanceof MediaTypes) {
                    $schema = $this->getSchemaFromMediaTypes($mediaTypes);
                    if ($schema instanceof TypeInterface) {
                        $result[] = new Response($statusCode, $schema);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @throws InvalidSchemaException
     */
    private function getSchemaFromMediaTypes(MediaTypes $mediaTypes): ?TypeInterface
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

        return $this->transformInlineStruct($type);
    }

    /**
     * TypeAPI can not handle inline struct references mostly in OpenAPI specs refer to the components section but in
     * case of an inline definition we automatically move it to the definitions and generate an inline type
     *
     * @throws InvalidSchemaException
     */
    private function transformInlineStruct(TypeInterface $type): TypeInterface
    {
        if ($type instanceof StructType) {
            // we have an inline struct type we automatically add this ot the definitions, since we have no name we generate
            // it based on the type, this should motivate users to move the definition to the components section
            $typeName = 'Inline' . substr($this->hashInspector->generateByType($type), 0, 8);
            $this->definitions->addType($typeName, $type);

            return TypeFactory::getReference($typeName);
        } elseif ($type instanceof MapType) {
            $child = $type->getAdditionalProperties();
            if ($child instanceof TypeInterface) {
                $r = $this->transformInlineStruct($child);
                return TypeFactory::getMap($r);
            }
        } elseif ($type instanceof ArrayType) {
            $child = $type->getItems();
            if ($child instanceof TypeInterface) {
                $r = $this->transformInlineStruct($child);
                return TypeFactory::getArray($r);
            }
        } elseif ($type instanceof UnionType) {
            $result = [];
            foreach ($type->getOneOf() as $child) {
                $result[] = $this->transformInlineStruct($child);
            }
            return TypeFactory::getUnion($result);
        } elseif ($type instanceof IntersectionType) {
            $result = [];
            foreach ($type->getAllOf() as $child) {
                $result[] = $this->transformInlineStruct($child);
            }
            return TypeFactory::getIntersection($result);
        }

        return $type;
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


    }

    private function getFirstSecurityScopes(array $security): ?array
    {
        foreach ($security as $securityRequirement) {
            $properties = $securityRequirement->getProperties();
            $scopes = reset($properties);
            if (is_array($scopes)) {
                return $scopes;
            }
        }

        return null;
    }

    private function getSchema(): SchemaInterface
    {
        return (new SchemaParser\Popo())->parse(OpenAPIModel::class);
    }

    public static function fromFile(string $file): SpecificationInterface
    {
        if (empty($file) || !is_file($file)) {
            throw new ParserException('Could not load OpenAPI schema ' . $file);
        }

        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array($extension, ['yaml', 'yml'])) {
            $data = json_encode(Yaml::parse(file_get_contents($file)));
        } else {
            $data = file_get_contents($file);
        }

        $basePath = pathinfo($file, PATHINFO_DIRNAME);
        $parser   = new OpenAPI($basePath);

        return $parser->parse($data);
    }
}
