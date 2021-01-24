<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Generator\Client;

use PSX\Api\GeneratorInterface;
use PSX\Api\Resource;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;
use PSX\Schema\Schema;
use PSX\Schema\Type\MapType;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\Type\StructType;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * LanguageAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class LanguageAbstract implements GeneratorInterface
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var Environment
     */
    protected $engine;

    /**
     * @param string $baseUrl
     * @param string $namespace
     */
    public function __construct(string $baseUrl, ?string $namespace = null)
    {
        $this->baseUrl   = $baseUrl;
        $this->namespace = $namespace;
        $this->engine    = $this->newTemplateEngine();
    }

    /**
     * @inheritDoc
     */
    public function generate(SpecificationInterface $specification)
    {
        $collection = $specification->getResourceCollection();
        $definitions = $specification->getDefinitions();

        $resources = [];

        $chunks = new Generator\Code\Chunks();
        foreach ($collection as $path => $resource) {
            $this->generateResource($resource, $definitions, $chunks, $resources);
        }

        $this->generateSchema($definitions, $chunks);
        $this->generateClient($resources, $chunks);

        return $chunks;
    }

    /**
     * @param Resource $resource
     * @param DefinitionsInterface $definitions
     * @param Generator\Code\Chunks $chunks
     * @param array $resources
     * @return void
     */
    public function generateResource(Resource $resource, DefinitionsInterface $definitions, Generator\Code\Chunks $chunks, array &$resources): void
    {
        $className = $this->getClassName($resource->getPath());
        if (empty($className)) {
            return;
        }

        $properties = $this->getPathParameterTypes($resource, $definitions);
        $urlParts = $this->getUrlParts($resource, $properties ?? []);

        $resources[$className] = [
            'description' => 'Endpoint: ' . $resource->getPath(),
            'methodName' => 'get' . substr($className, 0, -8),
            'path' => $resource->getPath(),
            'properties' => $properties
        ];

        $methods = [];
        foreach ($resource->getMethods() as $method) {
            $methodName = $this->getMethodName($method->getOperationId() ?: strtolower($method->getName()));

            $args = [];
            $docs = [];

            // query parameters
            if ($method->hasQueryParameters()) {
                $queryParameters = $method->getQueryParameters();

                $args['query'] = $this->getType(TypeFactory::getReference($queryParameters));
                $docs['query'] = $this->getDocType(TypeFactory::getReference($queryParameters));
            }

            // request
            $request = $method->getRequest();
            if (!empty($request) && !in_array($method->getName(), ['GET', 'DELETE'])) {
                $type = $this->resolveType($request, $definitions);

                $args['data'] = $this->getType($type);
                $docs['data'] = $this->getDocType($type);
            }

            // response
            $response = $this->getSuccessfulResponse($method);
            if (!empty($response)) {
                $type = $this->resolveType($response, $definitions);

                $return = $this->getType($type);
                $returnDoc = $this->getDocType($type);
            } else {
                $return = null;
                $returnDoc = null;
            }

            $methods[$methodName] = [
                'httpMethod' => $method->getName(),
                'description' => $method->getDescription(),
                'secure' => $method->hasSecurity(),
                'args' => $args,
                'docs' => $docs,
                'return' => $return,
                'returnDoc' => $returnDoc,
            ];
        }

        $code = $this->engine->render($this->getTemplate(), [
            'baseUrl' => $this->baseUrl,
            'namespace' => $this->namespace,
            'className' => $className,
            'urlParts' => $urlParts,
            'resource' => $resource,
            'properties' => $properties,
            'methods' => $methods,
        ]);

        $chunks->append($this->getFileName($className), $this->getFileContent($code, $className));
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @param array $args
     * @return array
     */
    protected function getUrlParts(Resource $resource, array $args): array
    {
        $result = [];
        reset($args);
        $parts = explode('/', $resource->getPath());
        foreach ($parts as $part) {
            if (isset($part[0]) && ($part[0] == ':' || $part[0] == '$')) {
                $pathName = key($args);
                if ($pathName === null) {
                    throw new \RuntimeException('Missing ' . $part . ' as path parameter');
                }

                $result[] = [
                    'type'  => 'variable',
                    'value' => $pathName,
                ];

                next($args);
            } elseif (!empty($part)) {
                $result[] = [
                    'type'  => 'string',
                    'value' => $part,
                ];
            }
        }

        return $result;
    }

    private function getPathParameterTypes(Resource $resource, DefinitionsInterface $definitions): ?array
    {
        if (!$resource->hasPathParameters()) {
            return null;
        }

        if (!$definitions->hasType($resource->getPathParameters())) {
            return null;
        }

        $type = $definitions->getType($resource->getPathParameters());
        if (!$type instanceof StructType) {
            return null;
        }

        $args = [];
        $properties = $type->getProperties();
        foreach ($properties as $name => $property) {
            $args[$name] = $this->getType($property);
        }

        return $args;
    }

    /**
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @return \PSX\Schema\SchemaInterface|null
     */
    protected function getSuccessfulResponse(Resource\MethodAbstract $method)
    {
        $responses = $method->getResponses();
        $codes = [200, 201];

        foreach ($codes as $code) {
            if (isset($responses[$code])) {
                return $responses[$code];
            }
        }

        return null;
    }

    /**
     * @param DefinitionsInterface $definitions
     * @param Generator\Code\Chunks $chunks
     */
    protected function generateSchema(DefinitionsInterface $definitions, Generator\Code\Chunks $chunks)
    {
        $schema = new Schema(TypeFactory::getAny(), $definitions);
        $result = $this->getGenerator()->generate($schema);
        
        if ($result instanceof Generator\Code\Chunks) {
            foreach ($result->getChunks() as $identifier => $code) {
                $chunks->append($this->getFileName($identifier), $this->getFileContent($code, $identifier));
            }
        } else {
            $chunks->append($this->getFileName('RootSchema'), $result);
        }
    }

    /**
     * @param array $resources
     * @param Generator\Code\Chunks $chunks
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function generateClient(array $resources, Generator\Code\Chunks $chunks)
    {
        $identifier = 'Client';

        $code = $this->engine->render($this->getClientTemplate(), [
            'baseUrl' => $this->baseUrl,
            'namespace' => $this->namespace,
            'resources' => $resources
        ]);

        $chunks->append($this->getFileName($identifier), $this->getFileContent($code, $identifier));
    }

    /**
     * Returns the type of the provided property for the specific language
     *
     * @param \PSX\Schema\TypeInterface $property
     * @return string
     */
    protected function getType(TypeInterface $property): string
    {
        $generator = $this->getGenerator();
        if ($generator instanceof Generator\TypeAwareInterface) {
            return $generator->getType($property);
        } else {
            return '';
        }
    }

    /**
     * Returns a type which is used in the documentation
     * 
     * @param \PSX\Schema\TypeInterface $property
     * @return string
     */
    protected function getDocType(TypeInterface $property): string
    {
        $generator = $this->getGenerator();
        if ($generator instanceof Generator\TypeAwareInterface) {
            return $generator->getDocType($property);
        } else {
            return '';
        }
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getClassName(string $path): string
    {
        $parts = explode('/', $path);
        $parts = array_map(function($part){
            return ucfirst(preg_replace('/[^A-Za-z0-9_]+/', '', $part));
        }, $parts);

        return implode('', $parts) . 'Resource';
    }

    /**
     * @param string $code
     * @param string $identifier
     * @return string
     */
    protected function getFileContent(string $code, string $identifier): string
    {
        return $code;
    }

    /**
     * @return string
     */
    abstract protected function getTemplate(): string;

    /**
     * @return string
     */
    abstract protected function getClientTemplate(): string;

    /**
     * @return \PSX\Schema\GeneratorInterface
     */
    abstract protected function getGenerator(): SchemaGeneratorInterface;

    /**
     * @param string $identifier
     * @return string
     */
    abstract protected function getFileName(string $identifier): string;

    /**
     * @param string $methodName
     * @return string
     */
    private function getMethodName(string $methodName): string
    {
        $parts = explode('_', str_replace(['.', ' '], '_', $methodName));
        $parts = array_map(function($part){
            return ucfirst(preg_replace('/[^A-Za-z0-9_]+/', '', $part));
        }, $parts);

        return lcfirst(implode('', $parts));
    }

    /**
     * Resolves a reference type in case it points to a non struct or map type
     *
     * @param string $name
     * @param DefinitionsInterface $definitions
     * @return ReferenceType|TypeInterface
     */
    private function resolveType(string $name, DefinitionsInterface $definitions): TypeInterface
    {
        $resolved = $definitions->getType($name);
        if (!$resolved instanceof StructType && !$resolved instanceof MapType) {
            return $resolved;
        }

        return TypeFactory::getReference($name);
    }

    /**
     * @return Environment
     */
    private function newTemplateEngine(): Environment
    {
        $loader = new FilesystemLoader([__DIR__ . '/Language']);
        $engine = new Environment($loader);

        return $engine;
    }
}
