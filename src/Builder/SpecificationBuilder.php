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

use PSX\Api\SecurityInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Schema\SchemaManagerInterface;

/**
 * SpecificationBuilder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SpecificationBuilder implements SpecificationBuilderInterface
{
    /**
     * @var SchemaManagerInterface
     */
    private $schemaManager;

    /**
     * @var SpecificationInterface
     */
    private $specification;

    public function __construct(SchemaManagerInterface $schemaManager)
    {
        $this->schemaManager = $schemaManager;
        $this->specification = new Specification();
    }

    public function setSecurity(SecurityInterface $security): void
    {
        $this->specification->setSecurity($security);
    }

    public function addResource(int $status, string $path): ResourceBuilderInterface
    {
        $builder = new ResourceBuilder($this->schemaManager, $status, $path, $this->specification->getDefinitions());
        $this->specification->getResourceCollection()->set($builder->getResource());
        return $builder;
    }

    public function getSpecification(): SpecificationInterface
    {
        return $this->specification;
    }
}
