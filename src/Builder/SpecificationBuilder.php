<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\Exception\OperationAlreadyExistsException;
use PSX\Api\SecurityInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\TypeInterface;

/**
 * SpecificationBuilder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class SpecificationBuilder implements SpecificationBuilderInterface
{
    private SpecificationInterface $specification;

    public function __construct()
    {
        $this->specification = new Specification();
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->specification->setBaseUrl($baseUrl);
    }

    public function setSecurity(SecurityInterface $security): void
    {
        $this->specification->setSecurity($security);
    }

    public function addOperation(string $operationId, string $method, string $path, int $statusCode, TypeInterface $schema): OperationBuilderInterface
    {
        if ($this->specification->getOperations()->has($operationId)) {
            throw new OperationAlreadyExistsException('Operation "' . $operationId . '" already exists');
        }

        $builder = new OperationBuilder($method, $path, $statusCode, $schema);
        $this->specification->getOperations()->add($operationId, $builder->getOperation());
        return $builder;
    }

    public function addDefinitions(DefinitionsInterface $definitions): self
    {
        $this->specification->getDefinitions()->merge($definitions);
        return $this;
    }

    public function addType(string $name, TypeInterface $schema): self
    {
        $this->specification->getDefinitions()->addType($name, $schema);
        return $this;
    }

    public function merge(SpecificationInterface $specification): self
    {
        $this->specification->merge($specification);
        return $this;
    }

    public function getSpecification(): SpecificationInterface
    {
        return $this->specification;
    }
}
