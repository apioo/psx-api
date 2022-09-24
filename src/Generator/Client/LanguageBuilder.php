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

use PSX\Api\Generator\Client\Util\Naming;
use PSX\Api\Resource;
use PSX\Api\SecurityInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator\Normalizer\NormalizerInterface;
use PSX\Schema\Generator\NormalizerAwareInterface;
use PSX\Schema\Generator\Type\GeneratorInterface as TypeGeneratorInterface;
use PSX\Schema\Generator\TypeAwareInterface;
use PSX\Schema\GeneratorInterface;
use PSX\Schema\Type\ArrayType;
use PSX\Schema\Type\IntersectionType;
use PSX\Schema\Type\MapType;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\Type\StructType;
use PSX\Schema\Type\UnionType;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;

/**
 * Class which transforms resource objects into language dtos which we use at the template engine to generate the client
 * code
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class LanguageBuilder
{
    private GeneratorInterface $generator;
    private TypeGeneratorInterface $typeGenerator;
    private NormalizerInterface $normalizer;
    private Naming $naming;

    public function __construct(GeneratorInterface $generator, Naming $naming)
    {
        $this->generator = $generator;
        $this->naming = $naming;

        if ($generator instanceof TypeAwareInterface) {
            $this->typeGenerator = $generator->getTypeGenerator();
        } else {
            throw new \RuntimeException('Provided generator is not type aware');
        }

        if ($generator instanceof NormalizerAwareInterface) {
            $this->normalizer = $generator->getNormalizer();
        } else {
            throw new \RuntimeException('Provided generator is not normalizer aware');
        }
    }

    public function getClient(SpecificationInterface $specification): Dto\Client
    {
        $security = null;
        if ($specification->getSecurity() instanceof SecurityInterface) {
            $security = $specification->getSecurity()->toArray();
        }

        $collection  = $specification->getResourceCollection();
        $definitions = $specification->getDefinitions();

        $resources = [];
        foreach ($collection as $resource) {
            $class = $this->getResource($resource, $definitions);
            if ($class === null) {
                continue;
            }

            $resources[$class->className] = $class;
        }

        return new Dto\Client(
            'Client',
            $resources,
            $security,
        );
    }

    private function getResource(Resource $resource, DefinitionsInterface $definitions): ?Dto\Resource
    {
        $className = $this->naming->buildClassNameByResource($resource);
        if (empty($className)) {
            return null;
        }


        $methodName = $this->naming->buildResourceGetter($className);
        $properties = $this->getPathParameters($resource, $definitions);
        $urlParts   = $this->getUrlParts($resource, $properties ?? []);
        $imports    = [];

        $methods = [];
        foreach ($resource->getMethods() as $method) {
            $methods[$this->naming->buildMethodNameByMethod($method)] = $this->getMethod($method, $definitions, $imports);
        }

        return new Dto\Resource(
            $className,
            $methodName,
            $resource->getPath(),
            $resource->getDescription(),
            $urlParts,
            $properties,
            $methods,
            $imports
        );
    }

    /**
     * @return Dto\UrlPart[]
     */
    private function getUrlParts(Resource $resource, array $args): array
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

                $result[] = new Dto\UrlPart('variable', $pathName);

                next($args);
            } elseif (!empty($part)) {
                $result[] = new Dto\UrlPart('string', $part);
            }
        }

        return $result;
    }

    /**
     * @return array<string, Dto\Type>|null
     */
    private function getPathParameters(Resource $resource, DefinitionsInterface $definitions): ?array
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
            $name = $this->normalizer->argument($name);

            $args[$name] = new Dto\Type(
                $this->typeGenerator->getType($property),
                $this->typeGenerator->getDocType($property),
                false
            );
        }

        return $args;
    }

    private function getMethod(Resource\MethodAbstract $method, DefinitionsInterface $definitions, array &$imports): Dto\Method
    {
        $args = new Dto\Arguments();

        // query parameters
        if ($method->hasQueryParameters()) {
            $query = TypeFactory::getReference($method->getQueryParameters());

            $args->query = new Dto\Type(
                $this->typeGenerator->getType($query),
                $this->typeGenerator->getDocType($query),
                true
            );

            $this->resolveImport($query, $imports);
        }

        // request
        $request = $method->getRequest();
        if (!empty($request) && !in_array($method->getName(), ['GET', 'DELETE'])) {
            $type = $this->resolveType($request, $definitions);

            $args->data = new Dto\Type(
                $this->typeGenerator->getType($type),
                $this->typeGenerator->getDocType($type),
                false
            );

            $this->resolveImport($type, $imports);
        }

        // response
        $response = $this->getSuccessfulResponse($method);
        if (!empty($response)) {
            $type = $this->resolveType($response, $definitions);

            $return = new Dto\Type(
                $this->typeGenerator->getType($type),
                $this->typeGenerator->getDocType($type),
            );

            $this->resolveImport($type, $imports);
        } else {
            $return = null;
        }

        return new Dto\Method(
            $method->getName(),
            $method->getDescription(),
            $method->hasSecurity(),
            $args,
            $return,
        );
    }

    private function getSuccessfulResponse(Resource\MethodAbstract $method): ?string
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
     * Resolves a reference type in case it points to a non struct or map type
     */
    private function resolveType(string $name, DefinitionsInterface $definitions): TypeInterface
    {
        $resolved = $definitions->getType($name);
        if (!$resolved instanceof StructType && !$resolved instanceof MapType) {
            return $resolved;
        }

        return TypeFactory::getReference($name);
    }

    private function resolveImport(TypeInterface $type, array &$imports): void
    {
        if ($type instanceof ReferenceType) {
            $imports[$type->getRef()] = $this->normalizer->class($type->getRef());
            if ($type->getTemplate()) {
                foreach ($type->getTemplate() as $t) {
                    $imports[$t] = $this->normalizer->class($type->getRef());
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
}
