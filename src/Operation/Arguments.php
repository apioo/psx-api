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

namespace PSX\Api\Operation;

use PSX\Api\Exception\ArgumentNotFoundException;
use PSX\Api\Exception\InvalidArgumentException;
use PSX\Api\Exception\OperationNotFoundException;
use PSX\Api\OperationInterface;
use PSX\Api\OperationsInterface;
use PSX\Schema\Type\StructType;
use PSX\Schema\TypeInterface;

/**
 * Arguments
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Arguments
{
    private array $container;

    public function __construct(array $arguments = [])
    {
        $this->container = $arguments;
    }

    public function add(string $name, Argument $argument): void
    {
        if ($argument->getSchema() instanceof StructType) {
            throw new InvalidArgumentException('It is not allowed to pass a struct type as argument, please add it to the definitions and pass a reference');
        }

        $this->container[$name] = $argument;
    }

    public function has(string $name): bool
    {
        return isset($this->container[$name]);
    }

    public function get(string $name): Argument
    {
        $argument = $this->container[$name] ?? null;
        if (!$argument instanceof Argument) {
            throw new ArgumentNotFoundException('Provided argument name does not exist');
        }

        return $argument;
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

    public function isEmpty(): bool
    {
        return count($this->container) === 0;
    }

    public function merge(Arguments $arguments): void
    {
        $this->container = array_merge($this->container, $arguments->getAll());
    }

    public function withAdded(Arguments $arguments): Arguments
    {
        return new Arguments(array_merge($this->container, $arguments->getAll()));
    }
}
