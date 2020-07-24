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

namespace PSX\Api;

use PSX\Api\Resource\MethodAbstract;
use PSX\Schema\Definitions;
use PSX\Schema\InvalidSchemaException;
use PSX\Schema\SchemaManagerInterface;
use PSX\Schema\Type\ReferenceType;

/**
 * Builder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Builder implements BuilderInterface
{
    /**
     * @var SchemaManagerInterface
     */
    private $schemaManager;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var Definitions
     */
    private $definitions;

    public function __construct(SchemaManagerInterface $schemaManager, int $status, string $path)
    {
        $this->schemaManager = $schemaManager;
        $this->resource = new Resource($status, $path);
        $this->definitions = new Definitions();
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title): void
    {
        $this->resource->setTitle($title);
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description): void
    {
        $this->resource->setDescription($description);
    }

    /**
     * @inheritDoc
     */
    public function setPathParameters(string $class): void
    {
        $this->resource->setPathParameters($this->getSchema($class));
    }

    /**
     * @inheritDoc
     */
    public function addMethod(MethodAbstract $method): MethodAbstract
    {
        $this->resource->addMethod($method);

        return $method;
    }

    /**
     * @inheritDoc
     */
    public function getSchema(string $class): string
    {
        $schema = $this->schemaManager->getSchema($class);
        $type = $schema->getType();

        if (!$type instanceof ReferenceType) {
            throw new InvalidSchemaException('Provided schema contains not a reference');
        }

        $this->definitions->merge($schema->getDefinitions());

        return $type->getRef();
    }

    /**
     * @inheritDoc
     */
    public function getSpecification(): SpecificationInterface
    {
        return Specification::fromResource($this->resource, $this->definitions);
    }
}
