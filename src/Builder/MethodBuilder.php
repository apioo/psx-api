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

namespace PSX\Api\Builder;

use PSX\Api\Resource\Factory;
use PSX\Api\Resource\MethodAbstract;
use PSX\Schema\Builder;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\InvalidSchemaException;
use PSX\Schema\SchemaManagerInterface;
use PSX\Schema\Type\ReferenceType;

/**
 * MethodBuilder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class MethodBuilder implements MethodBuilderInterface
{
    /**
     * @var SchemaManagerInterface
     */
    private $schemaManager;

    /**
     * @var DefinitionsInterface
     */
    private $definitions;

    /**
     * @var MethodAbstract
     */
    private $method;

    public function __construct(SchemaManagerInterface $schemaManager, DefinitionsInterface $definitions, string $methodName)
    {
        $this->schemaManager = $schemaManager;
        $this->definitions = $definitions;
        $this->method = Factory::getMethod($methodName);
    }

    /**
     * @inheritDoc
     */
    public function setOperationId(?string $operationId): void
    {
        $this->method->setOperationId($operationId);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(?string $description): void
    {
        $this->method->setDescription($description);
    }

    /**
     * @inheritDoc
     */
    public function setQueryParameters(?string $typeName): Builder
    {
        $builder = new Builder();
        $this->definitions->addType($typeName, $builder->getType());
        $this->method->setQueryParameters($typeName);

        return $builder;
    }

    /**
     * @inheritDoc
     */
    public function setRequest(string $schemaName): void
    {
        $this->method->setRequest($this->getSchema($schemaName));
    }

    /**
     * @inheritDoc
     */
    public function addResponse(int $statusCode, string $schemaName): void
    {
        $this->method->addResponse($statusCode, $this->getSchema($schemaName));
    }

    /**
     * @inheritDoc
     */
    public function setSecurity(string $name, array $scopes): void
    {
        $this->method->setSecurity($name, $scopes);
    }

    /**
     * @inheritDoc
     */
    public function setTags(array $tags): void
    {
        $this->method->setTags($tags);
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): MethodAbstract
    {
        return $this->method;
    }

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
