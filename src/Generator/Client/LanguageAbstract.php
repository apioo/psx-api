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

namespace PSX\Api\Generator\Client;

use PSX\Api\GeneratorCollectionInterface;
use PSX\Api\GeneratorInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Schema\Definitions;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;
use PSX\Schema\Schema;
use PSX\Schema\SchemaInterface;
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
abstract class LanguageAbstract implements GeneratorInterface, GeneratorCollectionInterface
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
     * @param string $baseUrl
     * @param string $namespace
     */
    public function __construct(string $baseUrl, ?string $namespace = null)
    {
        $this->baseUrl = $baseUrl;
        $this->namespace = $namespace;
    }

    /**
     * @inheritdoc
     */
    public function generate(Resource $resource)
    {
        $loader = new FilesystemLoader([__DIR__ . '/Language']);
        $engine = new Environment($loader);

        $definitions = new Definitions();
        $className = $this->getClassName($resource->getPath());

        if (empty($className)) {
            return;
        }

        if ($resource->hasPathParameters()) {
            $pathParameters = $resource->getPathParameters();

            $name = 'Path';
            $definitions->addType($name, $pathParameters);
        }

        $properties = $this->getProperties($resource);
        $urlParts = $this->getUrlParts($resource, $properties);

        $methods = [];
        foreach ($resource->getMethods() as $method) {
            $methodName = $this->getMethodName($method->getOperationId() ?: strtolower($method->getName()));

            $args = [];
            $docs = [];

            // query parameters
            if ($method->hasQueryParameters()) {
                $queryParameters = $method->getQueryParameters();

                $name = ucfirst(strtolower($method->getName())) . 'Query';
                $definitions->addType($name, $queryParameters);

                $args['query'] = $this->getType(TypeFactory::getReference($name));
                $docs['query'] = $this->getDocType(TypeFactory::getReference($name));
            }

            // request
            $request = $method->getRequest();
            if ($request instanceof SchemaInterface && !in_array($method->getName(), ['GET', 'DELETE'])) {
                $definitions->merge($request->getDefinitions());

                $type = $this->resolveType($definitions, $request->getType());

                $args['data'] = $this->getType($type);
                $docs['data'] = $this->getDocType($type);
            }

            // response
            $response = $this->getSuccessfulResponse($method);
            if ($response instanceof SchemaInterface) {
                $definitions->merge($response->getDefinitions());

                $type = $this->resolveType($definitions, $response->getType());

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

        $code = $engine->render($this->getTemplate(), [
            'baseUrl' => $this->baseUrl,
            'namespace' => $this->namespace,
            'className' => $className,
            'urlParts' => $urlParts,
            'resource' => $resource,
            'properties' => $properties,
            'methods' => $methods,
        ]);

        $chunks = new Generator\Code\Chunks();
        $chunks->append($this->getFileName($className), $this->getFileContent($code, $className));

        $this->generateSchema($definitions, $chunks);

        return $chunks;
    }

    /**
     * @param ResourceCollection $collection
     * @return Generator\Code\Chunks|string
     */
    public function generateAll(ResourceCollection $collection)
    {
        $chunks = new Generator\Code\Chunks();
        foreach ($collection as $path => $resource) {
            $chunks->merge($this->generate($resource));
        }

        return $chunks;
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

    /**
     * @param \PSX\Api\Resource $resource
     * @return array
     */
    protected function getProperties(Resource $resource)
    {
        $args = [];
        if ($resource->hasPathParameters()) {
            $properties = $resource->getPathParameters()->getProperties();
            foreach ($properties as $name => $property) {
                $args[$name] = $this->getType($property);
            }
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
     * @return \PSX\Schema\GeneratorInterface
     */
    abstract protected function getGenerator(): SchemaGeneratorInterface;

    /**
     * @param string $identifier
     * @return string
     */
    abstract protected function getFileName(string $identifier): string;

    /**
     * @param $methodName
     * @return string
     */
    private function getMethodName($methodName): string
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
     * @param DefinitionsInterface $definitions
     * @param TypeInterface $type
     * @return ReferenceType|TypeInterface
     */
    private function resolveType(DefinitionsInterface $definitions, TypeInterface $type)
    {
        if ($type instanceof ReferenceType) {
            $resolved = $definitions->getType($type->getRef());
            if (!$resolved instanceof StructType && !$resolved instanceof MapType) {
                return $resolved;
            }
        }

        return $type;
    }
}
