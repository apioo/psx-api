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

namespace PSX\Api\Parser;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Api\ParserCollectionInterface;
use PSX\Api\ParserInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Api\Util\Inflection;
use PSX\Json\Parser;
use PSX\Model\OpenAPI\MediaType;
use PSX\Model\OpenAPI\MediaTypes;
use PSX\Model\OpenAPI\OpenAPI as OpenAPIModel;
use PSX\Model\OpenAPI\Operation;
use PSX\Model\OpenAPI\Parameter;
use PSX\Model\OpenAPI\PathItem;
use PSX\Model\OpenAPI\Reference;
use PSX\Model\OpenAPI\RequestBody;
use PSX\Model\OpenAPI\Response;
use PSX\Model\OpenAPI\Responses;
use PSX\Schema\Definitions;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Parser as SchemaParser;
use PSX\Schema\Schema;
use PSX\Schema\SchemaInterface;
use PSX\Schema\SchemaTraverser;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\Type\StructType;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;
use PSX\Schema\Visitor\TypeVisitor;
use RuntimeException;

/**
 * OpenAPI
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OpenAPI implements ParserInterface
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var string|null
     */
    private $basePath;

    /**
     * @var \PSX\Schema\Parser\TypeSchema
     */
    private $schemaParser;

    /**
     * @var \PSX\Schema\DefinitionsInterface
     */
    private $definitions;

    /**
     * @var \PSX\Model\OpenAPI\OpenAPI
     */
    private $document;

    /**
     * @param Reader $annotationReader
     * @param string|null $basePath
     */
    public function __construct(Reader $annotationReader, ?string $basePath = null)
    {
        $this->annotationReader = $annotationReader;
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
            $this->definitions
        );
    }

    private function parseResource(PathItem $data, string $path): Resource
    {
        $status   = Resource::STATUS_ACTIVE;
        $resource = new Resource($status, $path);

        $resource->setTitle($data->getSummary());
        $resource->setDescription($data->getDescription());

        $this->parseUriParameters($resource, $data);

        $methods = [
            'get' => $data->getGet(),
            'post' => $data->getPost(),
            'put' => $data->getPut(),
            'delete' => $data->getDelete(),
            'patch' => $data->getPatch(),
        ];

        $typePrefix = $this->getTypePrefix($path);

        foreach ($methods as $methodName => $operation) {
            if (!$operation instanceof Operation) {
                continue;
            }

            $method = Resource\Factory::getMethod(strtoupper($methodName));

            $method->setOperationId($operation->getOperationId());
            $method->setDescription($operation->getSummary());
            $method->setTags($operation->getTags() ?? []);

            $this->parseQueryParameters($method, $operation);
            $this->parseRequest($method, $operation->getRequestBody(), $typePrefix);
            $this->parseResponses($method, $operation, $typePrefix);

            $resource->addMethod($method);
        }

        return $resource;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param array $data
     */
    private function parseUriParameters(Resource $resource, PathItem $data)
    {
        $type = $this->parseParameters('path', $data->getParameters() ?? []);

        $typeName = 'Path';
        $this->definitions->addType($typeName, $type);

        $resource->setPathParameters($typeName);

    }

    /**
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @param Operation $data
     */
    private function parseQueryParameters(Resource\MethodAbstract $method, Operation $data)
    {
        $type = $this->parseParameters('query', $data->getParameters() ?? []);

        $typeName = ucfirst(strtolower($method->getName())) . 'Query';
        $this->definitions->addType($typeName, $type);

        $method->setQueryParameters($typeName);
    }

    /**
     * @param string $type
     * @param array $data
     * @return StructType
     * @throws \PSX\Schema\TypeNotFoundException
     */
    private function parseParameters(string $type, array $data): StructType
    {
        $return = TypeFactory::getStruct();
        $required   = [];

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

        $return->setRequired($required);

        return $return;
    }

    /**
     * @param string $in
     * @param Parameter|Reference $data
     * @return array|\PSX\Schema\TypeInterface
     * @throws \PSX\Schema\TypeNotFoundException
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
            return $this->parseRequest($method, $this->resolveReference($requestBody->getRef()), $typePrefix);
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
        $parser = new SchemaParser\Popo($this->annotationReader);
        $schema = $parser->parse(OpenAPIModel::class);

        $this->definitions = $this->schemaParser->parseSchema($data)->getDefinitions();
        $this->document    = (new SchemaTraverser())->traverse($data, $schema, new TypeVisitor());
    }

    /**
     * @param string $path
     * @return string
     */
    private function getTypePrefix(string $path): string
    {
        $parts = explode('/', $path);
        $parts = array_map(function($part){
            return ucfirst(preg_replace('/[^A-Za-z0-9_]+/', '', $part));
        }, $parts);

        return implode('', $parts);
    }

    public static function fromFile(string $file, string $path): SpecificationInterface
    {
        if (!empty($file) && is_file($file)) {
            $reader = new SimpleAnnotationReader();
            $reader->addNamespace('PSX\\Schema\\Annotation');

            $basePath = pathinfo($file, PATHINFO_DIRNAME);
            $parser   = new OpenAPI($reader, $basePath);

            return $parser->parse(file_get_contents($file), $path);
        } else {
            throw new RuntimeException('Could not load OpenAPI schema ' . $file);
        }
    }
}
