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

namespace PSX\Api;

use PSX\Api\Exception\OperationNotFoundException;
use PSX\Api\Scanner\FilterInterface;

/**
 * Operations
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Operations implements OperationsInterface, \JsonSerializable
{
    private array $container;

    public function __construct(array $operations = [])
    {
        $this->container = $operations;
    }

    public function add(string $name, OperationInterface $operation): void
    {
        $this->container[$name] = $operation;
    }

    public function has(string $name): bool
    {
        return isset($this->container[$name]);
    }

    public function get(string $name): OperationInterface
    {
        $operation = $this->container[$name] ?? null;
        if (!$operation instanceof OperationInterface) {
            throw new OperationNotFoundException('Provided operation name does not exist');
        }

        return $operation;
    }

    public function getAll(): array
    {
        return $this->container;
    }

    public function remove(string $name): void
    {
        if (isset($this->container[$name])) {
            unset($this->container[$name]);
        }
    }

    public function merge(OperationsInterface $operations): void
    {
        $this->container = array_merge($this->container, $operations->getAll());
    }

    public function filter(FilterInterface $filter): void
    {
        $this->container = array_filter($this->container, static function(OperationInterface $operation) use ($filter): bool {
            return $filter->match($operation);
        });
    }

    public function withAdded(OperationsInterface $operations): Operations
    {
        return new Operations(array_merge($this->container, $operations->getAll()));
    }

    public function jsonSerialize(): array
    {
        return $this->container;
    }
}
