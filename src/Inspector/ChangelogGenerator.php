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
use PSX\Api\OperationsInterface;
use PSX\Api\SecurityInterface;
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
        if ($left->getBaseUrl() !== $right->getBaseUrl()) {
            yield SemVer::PATCH => $this->getMessageChanged(['baseUrl'], $left->getBaseUrl(), $right->getBaseUrl(), 'Specification');
        }

        if ($left->getSecurity() !== null && $right->getSecurity() !== null) {
            yield from $this->generateSecurity($left->getSecurity(), $right->getSecurity());
        } elseif ($left->getSecurity() !== null && $right->getSecurity() === null) {
            yield SemVer::PATCH => 'Security settings was removed';
        } elseif ($left->getSecurity() === null && $right->getSecurity() !== null) {
            yield SemVer::PATCH => 'Security settings was added';
        }

        yield from $this->generateCollection($left->getOperations(), $right->getOperations());
        yield from $this->changelogGenerator->generate($left->getDefinitions(), $right->getDefinitions());
    }

    private function generateSecurity(SecurityInterface $left, SecurityInterface $right): \Generator
    {
        $leftData = $left->toArray();
        $rightData = $right->toArray();

        foreach ($leftData as $key => $value) {
            if (isset($rightData[$key])) {
                if ($value !== $rightData[$key]) {
                    yield SemVer::PATCH => $this->getMessageChanged([$key], $value, $rightData[$key], 'Security');
                }
            } else {
                yield SemVer::PATCH => $this->getMessageRemoved([$key], 'Security');
            }
        }

        foreach ($rightData as $key => $value) {
            if (!isset($leftData[$key])) {
                yield SemVer::PATCH => $this->getMessageAdded([$key], 'Security');
            }
        }
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
        if ($left->getMethod() !== $right->getMethod()) {
            yield SemVer::PATCH => $this->getMessageChanged([$name, 'method'], $left->getMethod(), $right->getMethod());
        }

        if ($left->getPath() !== $right->getPath()) {
            yield SemVer::PATCH => $this->getMessageChanged([$name, 'path'], $left->getPath(), $right->getPath());
        }

        yield from $this->generateResponse($left->getReturn(), $right->getReturn(), [$name, 'return']);

        foreach ($left->getArguments()->getAll() as $argumentName => $argument) {
            if ($right->getArguments()->has($argumentName)) {
                yield from $this->generateArgument($argument, $right->getArguments()->get($argumentName), [$name, 'arguments', $argumentName]);
            } else {
                yield SemVer::MAJOR => $this->getMessageRemoved([$name, 'arguments', $argumentName]);
            }
        }

        foreach ($right->getArguments()->getAll() as $argumentName => $argument) {
            if (!$left->getArguments()->has($argumentName)) {
                yield SemVer::MINOR => $this->getMessageAdded([$name, 'arguments', $argumentName]);
            }
        }

        foreach ($left->getThrows() as $index => $throw) {
            if (isset($right->getThrows()[$index])) {
                yield from $this->generateResponse($throw, $right->getThrows()[$index], [$name, 'throws', $throw->getCode()]);
            } else {
                yield SemVer::MINOR => $this->getMessageRemoved([$name, 'throws', $throw->getCode()]);
            }
        }

        foreach ($right->getThrows() as $index => $throw) {
            if (!isset($left->getThrows()[$index])) {
                yield SemVer::MINOR => $this->getMessageAdded([$name, 'throws', $throw->getCode()]);
            }
        }

        if ($left->getDescription() !== $right->getDescription()) {
            yield SemVer::PATCH => $this->getMessageChanged([$name, 'description'], $left->getDescription(), $right->getDescription());
        }

        if ($left->getStability() !== $right->getStability()) {
            yield SemVer::PATCH => $this->getMessageChanged([$name, 'stability'], $left->getStability(), $right->getStability());
        }

        $leftSecurity = implode(', ', $left->getSecurity());
        $rightSecurity = implode(', ', $right->getSecurity());
        if ($leftSecurity !== $rightSecurity) {
            yield SemVer::PATCH => $this->getMessageChanged([$name, 'security'], $leftSecurity, $rightSecurity);
        }

        if ($left->hasAuthorization() !== $right->hasAuthorization()) {
            yield SemVer::PATCH => $this->getMessageChanged([$name, 'authorization'], $left->hasAuthorization(), $right->hasAuthorization());
        }

        $leftTags = implode(', ', $left->getTags());
        $rightTags = implode(', ', $right->getTags());
        if ($leftTags !== $rightTags) {
            yield SemVer::PATCH => $this->getMessageChanged([$name, 'tags'], $leftTags, $rightTags);
        }
    }

    private function generateArgument(Operation\Argument $left, Operation\Argument $right, array $path): \Generator
    {
        if ($left->getIn() != $right->getIn()) {
            yield SemVer::PATCH => $this->getMessageChanged(array_merge($path, ['in']), $left->getIn(), $right->getIn());
        }

        yield from $this->changelogGenerator->generateType($left->getSchema(), $right->getSchema(), implode('.', $path));
    }

    private function generateResponse(Operation\Response $left, Operation\Response $right, array $path): \Generator
    {
        if ($left->getCode() != $right->getCode()) {
            yield SemVer::PATCH => $this->getMessageChanged(array_merge($path, ['code']), $left->getCode(), $right->getCode());
        }

        yield from $this->changelogGenerator->generateType($left->getSchema(), $right->getSchema(), implode('.', $path));
    }

    private function getMessageAdded(array $path, string $type = 'Operation'): string
    {
        return $type . ' "' . implode('.', $path) . '" was added';
    }

    private function getMessageRemoved(array $path, string $type = 'Operation'): string
    {
        return $type . ' "' . implode('.', $path) . '" was removed';
    }

    private function getMessageChanged(array $path, mixed $from, mixed $to, string $type = 'Operation'): string
    {
        $from = $from ?? 'NULL';
        $to = $to ?? 'NULL';

        return $type . ' "' . implode('.', $path) . '" has changed from "' . $from . '" to "' . $to . '"';
    }
}
