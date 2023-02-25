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

namespace PSX\Api\Inspector;

use PSX\Api\Exception\OperationNotFoundException;
use PSX\Api\Operation;
use PSX\Api\OperationInterface;
use PSX\Api\Operations;
use PSX\Api\OperationsInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Inspector\ChangelogGenerator as SchemaChangelogGenerator;
use PSX\Schema\Inspector\SemVer;

/**
 * ChangelogGenerator
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ChangelogGenerator
{
    private SchemaChangelogGenerator $changelogGenerator;

    public function __construct()
    {
        $this->changelogGenerator = new SchemaChangelogGenerator();
    }

    /**
     * @throws OperationNotFoundException
     */
    public function generate(SpecificationInterface $left, SpecificationInterface $right): \Generator
    {
        yield from $this->generateCollection($left->getOperations(), $right->getOperations());
        yield from $this->changelogGenerator->generate($left->getDefinitions(), $right->getDefinitions());
    }

    /**
     * @throws OperationNotFoundException
     */
    private function generateCollection(OperationsInterface $left, OperationsInterface $right): \Generator
    {
        foreach ($left->getAll() as $name => $operation) {
            if ($right->has($name)) {
                yield from $this->generateOperation($operation, $right->get($name), $name);
            } else {
                yield SemVer::MAJOR => $this->getMessageRemoved([$name]);
            }
        }

        foreach ($right->getAll() as $name => $operation) {
            if (!$left->has($name)) {
                yield SemVer::MINOR => $this->getMessageAdded([$name]);
            }
        }
    }

    private function generateOperation(OperationInterface $left, OperationInterface $right, string $name): \Generator
    {
        $this->generateResponse($left->getReturn(), $right->getReturn(), [$name, 'return']);

        if ($left->getMethod() !== $right->getMethod()) {
            yield SemVer::MINOR => $this->getMessageChanged([$name, 'method'], $left->getMethod(), $right->getMethod());
        }

        if ($left->getPath() !== $right->getPath()) {
            yield SemVer::MINOR => $this->getMessageChanged([$name, 'path'], $left->getPath(), $right->getPath());
        }

        if ($left->getDescription() !== $right->getDescription()) {
            yield SemVer::MINOR => $this->getMessageChanged([$name, 'description'], $left->getDescription(), $right->getDescription());
        }

        foreach ($left->getArguments() as $argumentName => $argument) {
            if ($right->getArguments()->has($argumentName)) {
                yield from $this->generateArgument($argument, $right->getArguments()->get($argumentName), [$name, 'arguments', $argumentName]);
            } else {
                yield SemVer::MAJOR => $this->getMessageRemoved([$name, 'arguments', $argumentName]);
            }
        }

        foreach ($right->getArguments() as $argumentName => $argument) {
            if (!$left->getArguments()->has($argumentName)) {
                yield SemVer::PATCH => $this->getMessageAdded([$name, 'arguments', $argumentName]);
            }
        }

        if ($left->hasAuthorization() !== $right->hasAuthorization()) {
            yield SemVer::MAJOR => $this->getMessageChanged([$name, 'authorization'], $left->hasAuthorization(), $right->hasAuthorization());
        }

        if ($left->getSecurity() !== $right->getSecurity()) {
            yield SemVer::MAJOR => $this->getMessageChanged([$name, 'security'], $left->getSecurity(), $right->getSecurity());
        }

        if ($left->isDeprecated() !== $right->isDeprecated()) {
            yield SemVer::PATCH => $this->getMessageChanged([$name, 'deprecated'], $left->isDeprecated(), $right->isDeprecated());
        }

        if ($left->getTags() !== $right->getTags()) {
            yield SemVer::PATCH => $this->getMessageChanged([$name, 'tags'], $left->getTags(), $right->getTags());
        }

        foreach ($left->getThrows() as $index => $throw) {
            if (isset($right->getThrows()[$index])) {
                yield from $this->generateResponse($throw, $right->getThrows()[$index], [$name, 'throws', $index]);
            } else {
                yield SemVer::MINOR => $this->getMessageRemoved([$name, 'throws', $index]);
            }
        }

        foreach ($right->getThrows() as $index => $throw) {
            if (!isset($left->getThrows()[$index])) {
                yield SemVer::PATCH => $this->getMessageAdded([$name, 'throws', $index]);
            }
        }
    }

    private function generateArgument(Operation\Argument $left, Operation\Argument $right, array $path): \Generator
    {
        if ($left->getIn() != $right->getIn()) {
            yield SemVer::MINOR => $this->getMessageChanged(array_merge($path, ['in']), $left->getIn(), $right->getIn());
        }

        yield from $this->changelogGenerator->generateType($left->getSchema(), $right->getSchema(), '');
    }

    private function generateResponse(Operation\Response $left, Operation\Response $right, array $path): \Generator
    {
        if ($left->getCode() != $right->getCode()) {
            yield SemVer::PATCH => $this->getMessageChanged(array_merge($path, ['code']), $left->getCode(), $right->getCode());
        }

        yield from $this->changelogGenerator->generateType($left->getSchema(), $right->getSchema(), '');
    }

    private function getMessageAdded(array $path): string
    {
        return 'Operation "' . implode('.', $path) . '" was added';
    }

    private function getMessageRemoved(array $path): string
    {
        return 'Operation "' . implode('.', $path) . '" was removed';
    }

    private function getMessageChanged(array $path, $from, $to): string
    {
        $from = $from ?? 'NULL';
        $to = $to ?? 'NULL';

        return 'Property "' . implode('.', $path) . '" has changed from "' . $from . '" to "' . $to . '"';
    }
}
