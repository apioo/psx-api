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

use PSX\Api\Exception\InvalidTypeException;
use PSX\Api\Generator\Client\Dto\Exception;
use PSX\Api\Generator\Client\Dto\Tag;
use PSX\Api\Generator\Client\Dto\Type;
use PSX\Api\Generator\Client\Util\Naming;
use PSX\Api\Operation\Argument;
use PSX\Api\OperationInterface;
use PSX\Api\OperationsInterface;
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

        $operations = [];
        $tags = [];
        $exceptions = [];

        $grouped = $this->groupOperationsByTag($specification->getOperations());
        if (count($grouped) > 1) {
            foreach ($grouped as $tagName => $tagOperations) {
                $exceptions = array_merge($exceptions, $this->getExceptions($tagOperations));
                $operations = $this->getOperations($tagOperations, $specification->getDefinitions());

                $tags[] = new Tag(
                    $this->naming->buildClassNameByTag($tagName),
                    $this->naming->buildMethodNameByTag($tagName),
                    $operations
                );
            }

            $operations = [];
        } else {
            $tagOperations = reset($grouped);
            if (!empty($tagOperations)) {
                $exceptions = array_merge($exceptions, $this->getExceptions($tagOperations));
                $operations = $this->getOperations($tagOperations, $specification->getDefinitions());
            }
        }

        return new Dto\Client(
            'Client',
            $operations,
            $tags,
            $exceptions,
            $security,
        );
    }

    private function getOperations(array $operations, DefinitionsInterface $definitions): array
    {
        $result = [];
        foreach ($operations as $operationId => $operation) {
            $methodName = $this->naming->buildMethodNameByOperationId($operationId);
            if (empty($methodName)) {
                continue;
            }

            $imports = [];
            $path = $pathNames = [];
            $query = $queryNames = [];
            $body = $bodyName = null;
            foreach ($operation->getArguments()->getAll() as $name => $argument) {
                $normalized = $this->normalizer->argument($name);
                if ($argument->getIn() === Argument::IN_PATH) {
                    $path[$normalized] = $this->newTypeBySchema($argument->getSchema(), false, $definitions);
                    $pathNames[$normalized] = $name;
                } elseif ($argument->getIn() === Argument::IN_QUERY) {
                    $query[$normalized] = $this->newTypeBySchema($argument->getSchema(), true, $definitions);
                    $queryNames[$normalized] = $name;
                } elseif ($argument->getIn() === Argument::IN_BODY) {
                    $body = $this->newTypeBySchema($argument->getSchema(), false, $definitions);
                    $bodyName = $normalized;
                }

                $this->resolveImport($argument->getSchema(), $imports);
            }

            $arguments = array_merge($path, $body !== null ? ['payload' => $body] : [], $query);

            $return = null;
            if (in_array($operation->getReturn()->getCode(), [200, 201])) {
                $return = $this->newTypeBySchema($operation->getReturn()->getSchema(), false, $definitions);

                $this->resolveImport($operation->getReturn()->getSchema(), $imports);
            }

            $throws = [];
            foreach ($operation->getThrows() as $throw) {
                $throws[$throw->getCode()] = $this->newTypeBySchema($throw->getSchema(), false, $definitions);

                $this->resolveImport($throw->getSchema(), $imports);
            }

            $result[] = new Dto\Operation(
                $methodName,
                $operation->getMethod(),
                $operation->getPath(),
                $operation->getDescription(),
                $arguments,
                $pathNames,
                $queryNames,
                $bodyName,
                $return,
                $throws,
                $imports
            );
        }

        return $result;
    }

    private function getExceptions(array $operations): array
    {
        $result = [];

        foreach ($operations as $operation) {
            $throws = $operation->getThrows();
            foreach ($throws as $throw) {
                $type = $throw->getSchema();
                if ($type instanceof ReferenceType) {
                    $className = $this->naming->buildClassNameByException($type->getRef());
                    $result[$className] = new Exception($className, $type->getRef(), 'The server returned an error');
                }
            }
        }

        return $result;
    }

    private function newTypeBySchema(TypeInterface $type, bool $optional, DefinitionsInterface $definitions): Type
    {
        if ($type instanceof ReferenceType) {
            // in case we have a reference type we take a look at the reference, normally this is a struct type but in
            // some special cases we need to extract the type
            $refType = $definitions->getType($type->getRef());
            if (!$refType instanceof StructType) {
                throw new InvalidTypeException('A reference can only point to a struct type, got: ' . get_class($refType));
            }
        }

        return new Dto\Type(
            $this->typeGenerator->getType($type),
            $this->typeGenerator->getDocType($type),
            $optional
        );
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

    private function groupOperationsByTag(OperationsInterface $operations): array
    {
        $result = [];
        foreach ($operations->getAll() as $operationId => $operation) {
            $tags = $operation->getTags();
            if (empty($tags)) {
                $tags = ['default'];
            }

            foreach ($tags as $tagName) {
                if (!isset($result[$tagName])) {
                    $result[$tagName] = [];
                }

                $result[$tagName][$operationId] = $operation;
            }
        }

        return $result;
    }
}
