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

namespace PSX\Api\Generator\Client;

use PSX\Api\Generator\Client\Dto\Type;
use PSX\Api\GeneratorInterface;
use PSX\Api\Resource;
use PSX\Api\SecurityInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;
use PSX\Schema\Schema;
use PSX\Schema\Type\ArrayType;
use PSX\Schema\Type\IntersectionType;
use PSX\Schema\Type\MapType;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\Type\StructType;
use PSX\Schema\Type\UnionType;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * LanguageAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class LanguageAbstract implements GeneratorInterface
{
    protected string $baseUrl;
    protected ?string $namespace;
    protected Environment $engine;

    public function __construct(string $baseUrl, ?string $namespace = null)
    {
        $this->baseUrl   = $baseUrl;
        $this->namespace = $namespace;
        $this->engine    = $this->newTemplateEngine();
    }

    /**
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    public function generate(SpecificationInterface $specification): Generator\Code\Chunks
    {
        $collection = $specification->getResourceCollection();
        $definitions = $specification->getDefinitions();

        $resources = [];

        $chunks = new Generator\Code\Chunks();
        foreach ($collection as $path => $resource) {
            $this->generateResource($resource, $definitions, $chunks, $resources);
        }

        $this->generateSchema($definitions, $chunks);
        $this->generateClient($resources, $specification, $chunks);

        return $chunks;
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     * @throws \PSX\Schema\Exception\TypeNotFoundException
     */
    public function generateResource(Resource $resource, DefinitionsInterface $definitions, Generator\Code\Chunks $chunks, array &$resources): void
    {
        $className = $this->buildClassNameByPath($resource->getPath());
        if (empty($className)) {
            return;
        }

        $imports = [];
        $properties = $this->getPathParameterTypes($resource, $definitions);
        $urlParts = $this->getUrlParts($resource, $properties ?? []);

        $resources[$className] = [
            'description' => 'Endpoint: ' . $resource->getPath(),
            'methodName' => 'get' . substr($className, 0, -8),
            'path' => $resource->getPath(),
            'tags' => $resource->getTags(),
            'properties' => $properties,
        ];

        $methods = [];
        foreach ($resource->getMethods() as $method) {
            $methodName = $this->buildMethodName($method->getOperationId() ?: strtolower($method->getName()));
            $args = [];

            // query parameters
            if ($method->hasQueryParameters()) {
                $query = TypeFactory::getReference($method->getQueryParameters());

                $args['query'] = new Type(
                    $this->getType($query),
                    $this->getDocType($query),
                    true
                );

                $this->resolveImport($query, $imports);
            }

            // request
            $request = $method->getRequest();
            if (!empty($request) && !in_array($method->getName(), ['GET', 'DELETE'])) {
                $type = $this->resolveType($request, $definitions);

                $args['data'] = new Type(
                    $this->getType($type),
                    $this->getDocType($type),
                    false
                );

                $this->resolveImport($type, $imports);
            }

            // response
            $response = $this->getSuccessfulResponse($method);
            if (!empty($response)) {
                $type = $this->resolveType($response, $definitions);

                $return = new Type(
                    $this->getType($type),
                    $this->getDocType($type),
                );

                $this->resolveImport($type, $imports);
            } else {
                $return = null;
            }

            $methods[$methodName] = [
                'httpMethod' => $method->getName(),
                'description' => $method->getDescription(),
                'secure' => $method->hasSecurity(),
                'args' => $args,
                'return' => $return,
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
            'imports' => $imports,
        ]);

        $chunks->append($this->getFileName($className), $this->getFileContent($code, $className));
    }

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
            $args[$name] = new Type(
                $this->getType($property),
                $this->getDocType($property),
                false
            );
        }

        return $args;
    }

    protected function getSuccessfulResponse(Resource\MethodAbstract $method): ?string
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

    protected function generateSchema(DefinitionsInterface $definitions, Generator\Code\Chunks $chunks): void
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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function generateClient(array $resources, SpecificationInterface $specification, Generator\Code\Chunks $chunks)
    {
        $security = null;
        if ($specification->getSecurity() instanceof SecurityInterface) {
            $security = $specification->getSecurity()->toArray();
        }

        $groups = $this->groupByTag($resources);
        if (count($groups) > 1) {
            // in case we have tags we create group layer
            $groupResources = [];
            foreach ($groups as $tag => $resources) {
                $className = $this->buildClassNameByTag($tag);

                $code = $this->engine->render($this->getGroupTemplate(), [
                    'baseUrl' => $this->baseUrl,
                    'namespace' => $this->namespace,
                    'className' => $className,
                    'resources' => $resources,
                ]);

                $chunks->append($this->getFileName($className), $this->getFileContent($code, $className));

                $groupResources[$className] = [
                    'description' => 'Tag: ' . $tag,
                    'methodName' => $this->buildMethodName($tag),
                    'properties' => [],
                ];
            }

            $className = 'Client';
            $code = $this->engine->render($this->getClientTemplate(), [
                'baseUrl' => $this->baseUrl,
                'namespace' => $this->namespace,
                'className' => $className,
                'resources' => $groupResources,
                'security' => $security,
            ]);

            $chunks->append($this->getFileName($className), $this->getFileContent($code, $className));
        } else {
            $className = 'Client';
            $code = $this->engine->render($this->getClientTemplate(), [
                'baseUrl' => $this->baseUrl,
                'namespace' => $this->namespace,
                'className' => $className,
                'resources' => $resources,
                'security' => $security,
            ]);

            $chunks->append($this->getFileName($className), $this->getFileContent($code, $className));
        }
    }

    /**
     * Group resources by tag
     */
    private function groupByTag(array $resources): array
    {
        $groups = [];
        foreach ($resources as $className => $resource) {
            $firstTag = $resource['tags'][0] ?? null;
            if (!empty($firstTag)) {
                $key = $firstTag;
            } else {
                $key = 'default';
            }

            if (!isset($groups[$key])) {
                $groups[$key] = [];
            }

            $groups[$key][$className] = $resource;
        }

        return $groups;
    }

    /**
     * Returns the type of the provided property for the specific language
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

    private function buildClassNameByPath(string $path): string
    {
        $parts = explode('/', $path);

        $result = [];
        $i = 0;
        foreach ($parts as $part) {
            if (str_starts_with($part, ':')) {
                $part = ($i === 0 ? 'By' : 'And') . ucfirst(substr($part, 1));
                $i++;
            } elseif (str_starts_with($part, '$')) {
                $part = ($i === 0 ? 'By' : 'And') . ucfirst(substr($part, 1, strpos($part, '<')));
                $i++;
            }

            $result[] = ucfirst(preg_replace('/[^A-Za-z0-9_]+/', '', $part));
        }

        $className = implode('', $result);
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $className)));

        return $className . 'Resource';
    }

    private function buildClassNameByTag(string $tag): string
    {
        $parts = explode('_', str_replace(['.', ' '], '_', $tag));
        $parts = array_map(function($part){
            return ucfirst(preg_replace('/[^A-Za-z0-9_]+/', '', $part));
        }, $parts);

        $className = implode('', $parts);

        return $className . 'Group';
    }

    protected function buildMethodName(string $methodName): string
    {
        $parts = explode('_', str_replace(['.', ' '], '_', $methodName));
        $parts = array_map(function($part){
            return ucfirst(preg_replace('/[^A-Za-z0-9_]+/', '', $part));
        }, $parts);

        return lcfirst(implode('', $parts));
    }

    protected function getFileContent(string $code, string $identifier): string
    {
        return $code;
    }

    abstract protected function getTemplate(): string;

    abstract protected function getGroupTemplate(): string;

    abstract protected function getClientTemplate(): string;

    abstract protected function getGenerator(): SchemaGeneratorInterface;

    abstract protected function getFileName(string $identifier): string;

    /**
     * Resolves a reference type in case it points to a non struct or map type
     *
     * @throws \PSX\Schema\Exception\TypeNotFoundException
     */
    private function resolveType(string $name, DefinitionsInterface $definitions): TypeInterface
    {
        $resolved = $definitions->getType($name);
        if (!$resolved instanceof StructType && !$resolved instanceof MapType) {
            return $resolved;
        }

        return TypeFactory::getReference($name);
    }

    private function resolveImport(TypeInterface $type, array &$imports)
    {
        if ($type instanceof ReferenceType) {
            $imports[$type->getRef()] = $type->getRef();
            if ($type->getTemplate()) {
                foreach ($type->getTemplate() as $t) {
                    $imports[$t] = $t;
                }
            }
        } elseif ($type instanceof MapType && $type->getAdditionalProperties() instanceof TypeInterface) {
            $this->resolveImport($type->getAdditionalProperties(), $imports);
        } elseif ($type instanceof ArrayType && $type->getItems() instanceof TypeInterface) {
            $this->resolveImport($type->getItems(), $imports);
        } elseif ($type instanceof UnionType && $type->getOneOf()) {
            foreach ($type->getOneOf() as $item) {
                $this->resolveImport($item, $imports);
            }
        } elseif ($type instanceof IntersectionType) {
            foreach ($type->getAllOf() as $item) {
                $this->resolveImport($item, $imports);
            }
        }
    }

    private function newTemplateEngine(): Environment
    {
        $loader = new FilesystemLoader([__DIR__ . '/Language']);
        $engine = new Environment($loader);

        return $engine;
    }
}
