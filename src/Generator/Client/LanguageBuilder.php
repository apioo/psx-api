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

namespace PSX\Api\Generator\Client;

use PSX\Api\Exception\InvalidTypeException;
use PSX\Api\Generator\Client\Util\Naming;
use PSX\Api\Operation\ArgumentInterface;
use PSX\Api\OperationInterface;
use PSX\Api\OperationsInterface;
use PSX\Api\SecurityInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Exception\TypeNotFoundException;
use PSX\Schema\Generator\Normalizer\NormalizerInterface;
use PSX\Schema\Generator\NormalizerAwareInterface;
use PSX\Schema\Generator\Type\GeneratorInterface as TypeGeneratorInterface;
use PSX\Schema\Generator\TypeAwareInterface;
use PSX\Schema\GeneratorInterface;
use PSX\Schema\Type\AnyType;
use PSX\Schema\Type\ArrayType;
use PSX\Schema\Type\IntersectionType;
use PSX\Schema\Type\MapType;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\Type\StructType;
use PSX\Schema\Type\UnionType;
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

    /**
     * @throws InvalidTypeException
     * @throws TypeNotFoundException
     */
    public function getClient(SpecificationInterface $specification, ?string $baseUrl, ?SecurityInterface $security): Dto\Client
    {
        $exceptions = [];

        $grouped = $this->groupOperations($specification->getOperations());

        [$tags, $operations] = $this->buildTags($grouped, $specification->getDefinitions(), $exceptions, []);

        return new Dto\Client(
            'Client',
            $operations,
            $tags,
            $exceptions,
            $security?->toArray(),
            $baseUrl,
        );
    }

    /**
     * @throws InvalidTypeException
     * @throws TypeNotFoundException
     */
    private function buildTags(array $grouped, DefinitionsInterface $definitions, array &$exceptions, array $path): array
    {
        $tags = [];
        $operations = [];
        foreach ($grouped as $key => $value) {
            if ($value instanceof OperationInterface) {
                $operations[$key] = $value;
            } elseif (is_array($value)) {
                [$subTags, $subOperations] = $this->buildTags($value, $definitions, $exceptions, array_merge($path, [$key]));

                $tags[] = new Dto\Tag(
                    $this->naming->buildClassNameByTag(array_merge($path, [$key])),
                    $this->naming->buildMethodNameByTag($key),
                    $subOperations,
                    $subTags
                );
            }
        }

        $operations = $this->getOperations($operations, $definitions, $exceptions);

        return [$tags, $operations];
    }

    /**
     * @param array<string, OperationInterface> $operations
     * @throws TypeNotFoundException
     * @throws InvalidTypeException
     */
    private function getOperations(array $operations, DefinitionsInterface $definitions, array &$exceptions): array
    {
        $result = [];
        foreach ($operations as $operationId => $operation) {
            $methodName = $this->naming->buildMethodNameByOperationId($operationId);
            if (empty($methodName)) {
                continue;
            }

            $imports = [];
            $path = $pathNames = [];
            $query = $queryNames = $queryStructNames = [];
            $body = $bodyName = null;
            foreach ($operation->getArguments()->getAll() as $name => $argument) {
                $realName = $argument->getName();
                if (empty($realName)) {
                    $realName = $name;
                }

                $normalized = $this->normalizer->argument($name);
                if ($argument->getIn() === ArgumentInterface::IN_PATH) {
                    $path[$normalized] = new Dto\Argument($argument->getIn(), $this->newType($argument->getSchema(), false, $definitions));
                    $pathNames[$normalized] = $realName;
                } elseif ($argument->getIn() === ArgumentInterface::IN_QUERY) {
                    $query[$normalized] = new Dto\Argument($argument->getIn(), $this->newType($argument->getSchema(), true, $definitions));
                    $queryNames[$normalized] = $realName;
                    if ($argument->getSchema() instanceof ReferenceType) {
                        $queryStructNames[] = $realName;
                    }
                } elseif ($argument->getIn() === ArgumentInterface::IN_BODY) {
                    $body = new Dto\Argument($argument->getIn(), $this->newType($argument->getSchema(), false, $definitions));
                    $bodyName = $normalized;
                }

                $this->resolveImport($argument->getSchema(), $imports);
            }

            if (!in_array($operation->getMethod(), ['POST', 'PUT', 'PATCH'])) {
                $body = null;
                $bodyName = null;
            }

            $arguments = array_merge($path, $body !== null ? [$bodyName => $body] : [], $query);

            $return = null;
            if (in_array($operation->getReturn()->getCode(), [200, 201, 202])) {
                $returnType = $this->newType($operation->getReturn()->getSchema(), false, $definitions);
                $innerSchema = $this->getInnerSchema($operation->getReturn()->getSchema(), $definitions);

                $return = new Dto\Response($operation->getReturn()->getCode(), $returnType, null, $innerSchema);

                $this->resolveImport($operation->getReturn()->getSchema(), $imports);
            }

            $throws = [];
            foreach ($operation->getThrows() as $throw) {
                $throwSchema = $throw->getSchema();

                $exceptionImports = [];
                $this->resolveImport($throwSchema, $exceptionImports);

                $exceptionType = $this->newType($throwSchema, false, $definitions);
                $innerSchema = $this->getInnerSchema($throwSchema, $definitions);

                $exceptionClassName = $this->naming->buildExceptionClassNameByType($throwSchema);
                $exceptions[$exceptionClassName] = new Dto\Exception($exceptionClassName, $exceptionType, 'The server returned an error', $exceptionImports);

                $throws[$throw->getCode()] = new Dto\Response($throw->getCode(), $exceptionType, $exceptionClassName, $innerSchema);

                $imports[$this->normalizer->file($exceptionClassName)] = $exceptionClassName;
            }

            $result[] = new Dto\Operation(
                $methodName,
                $operation->getMethod(),
                $operation->getPath(),
                $operation->getDescription(),
                $arguments,
                $return,
                $throws,
                $pathNames,
                $queryNames,
                $queryStructNames,
                $bodyName,
                $imports
            );
        }

        return $result;
    }

    private function getInnerSchema(TypeInterface $type, DefinitionsInterface $definitions): ?Dto\Type
    {
        if ($type instanceof MapType) {
            $type = $this->newType($type->getAdditionalProperties(), false, $definitions);
            $type->isMap = true;
            return $type;
        } elseif ($type instanceof ArrayType) {
            $type = $this->newType($type->getItems(), false, $definitions);
            $type->isArray = true;
            return $type;
        } else {
            return null;
        }
    }

    /**
     * @throws InvalidTypeException
     * @throws TypeNotFoundException
     */
    private function newType(TypeInterface $type, bool $optional, DefinitionsInterface $definitions): Dto\Type
    {
        if ($type instanceof ReferenceType) {
            // in case we have a reference type we take a look at the reference, normally this is a struct type but in
            // some special cases we need to extract the type
            $refType = $definitions->getType($type->getRef());
            if ($refType instanceof ReferenceType) {
                $refType = $definitions->getType($refType->getRef());
            }

            if (!$refType instanceof StructType && !$refType instanceof MapType && !$refType instanceof AnyType) {
                throw new InvalidTypeException('A reference can only point to a struct or map type, got: ' . get_class($refType) . ' for reference: ' . $type->getRef());
            }
        }

        return new Dto\Type(
            $this->typeGenerator->getType($type),
            $this->typeGenerator->getDocType($type),
            $optional
        );
    }

    private function resolveImport(TypeInterface $type, array &$imports): void
    {
        if ($type instanceof ReferenceType) {
            $imports[$this->normalizer->file($type->getRef())] = $this->normalizer->class($type->getRef());
            if ($type->getTemplate()) {
                foreach ($type->getTemplate() as $typeRef) {
                    $imports[$this->normalizer->file($typeRef)] = $this->normalizer->class($typeRef);
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

    private function groupOperations(OperationsInterface $operations): array
    {
        $result = [];
        foreach ($operations->getAll() as $operationId => $operation) {
            $parts = explode('.', $operationId);

            $last = &$result;
            foreach ($parts as $partName) {
                if (!isset($last[$partName])) {
                    $last[$partName] = [];
                }

                $last = &$last[$partName];
            }

            $last = $operation;
        }

        return $result;
    }
}
