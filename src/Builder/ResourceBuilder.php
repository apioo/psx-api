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

use PSX\Api\Resource;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Builder;
use PSX\Schema\Definitions;
use PSX\Schema\SchemaManagerInterface;

/**
 * Builder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceBuilder implements ResourceBuilderInterface
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

    /**
     * @var SpecificationInterface
     */
    private $specification;

    public function __construct(SchemaManagerInterface $schemaManager, int $status, string $path)
    {
        $this->schemaManager = $schemaManager;
        $this->resource      = new Resource($status, $path);
        $this->definitions   = new Definitions();
        $this->specification = Specification::fromResource($this->resource, $this->definitions);
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
    public function setPathParameters(string $typeName): Builder
    {
        $builder = new Builder();
        $this->definitions->addType($typeName, $builder->getType());
        $this->resource->setPathParameters($typeName);

        return $builder;
    }

    /**
     * @inheritDoc
     */
    public function addMethod(string $methodName): MethodBuilderInterface
    {
        $builder = new MethodBuilder($this->schemaManager, $this->definitions, $methodName);
        $this->resource->addMethod($builder->getMethod());

        return $builder;
    }

    /**
     * @inheritDoc
     */
    public function setTags(array $tags): void
    {
        $this->resource->setTags($tags);
    }

    /**
     * @inheritDoc
     */
    public function addResource(int $status, string $path): ResourceBuilderInterface
    {
        $builder = new static($this->schemaManager, $status, $path);
        $builder->definitions = $this->definitions;
        $builder->specification = $this->specification;

        $this->specification->getResourceCollection()->set($builder->resource);

        return $builder;
    }

    /**
     * @inheritDoc
     */
    public function getSpecification(): SpecificationInterface
    {
        return $this->specification;
    }
}
