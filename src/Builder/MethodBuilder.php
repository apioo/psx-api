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

namespace PSX\Api\Builder;

use PSX\Api\Exception\InvalidMethodException;
use PSX\Api\Resource\Factory;
use PSX\Api\Resource\MethodAbstract;
use PSX\Schema\Builder;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Exception\InvalidSchemaException;
use PSX\Schema\SchemaManagerInterface;
use PSX\Schema\Type\ReferenceType;

/**
 * MethodBuilder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class MethodBuilder implements MethodBuilderInterface
{
    private SchemaManagerInterface $schemaManager;
    private DefinitionsInterface $definitions;
    private MethodAbstract $method;

    /**
     * @throws InvalidMethodException
     */
    public function __construct(SchemaManagerInterface $schemaManager, DefinitionsInterface $definitions, string $methodName)
    {
        $this->schemaManager = $schemaManager;
        $this->definitions = $definitions;
        $this->method = Factory::getMethod($methodName);
    }

    public function setOperationId(?string $operationId): void
    {
        $this->method->setOperationId($operationId);
    }

    public function setDescription(?string $description): void
    {
        $this->method->setDescription($description);
    }

    public function setQueryParameters(?string $typeName): Builder
    {
        $builder = new Builder();
        $this->definitions->addType($typeName, $builder->getType());
        $this->method->setQueryParameters($typeName);

        return $builder;
    }

    public function setRequest(string $schemaName): void
    {
        $this->method->setRequest($this->getSchema($schemaName));
    }

    public function addResponse(int $statusCode, string $schemaName): void
    {
        $this->method->addResponse($statusCode, $this->getSchema($schemaName));
    }

    public function setSecurity(string $name, array $scopes): void
    {
        $this->method->setSecurity($name, $scopes);
    }

    public function setTags(array $tags): void
    {
        $this->method->setTags($tags);
    }

    public function getMethod(): MethodAbstract
    {
        return $this->method;
    }

    /**
     * @throws InvalidSchemaException
     */
    private function getSchema(string $schemaName): string
    {
        $schema = $this->schemaManager->getSchema($schemaName);
        $type = $schema->getType();

        if (!$type instanceof ReferenceType) {
            throw new InvalidSchemaException('Provided schema contains not a reference');
        }

        $this->definitions->merge($schema->getDefinitions());

        return $type->getRef();
    }
}
