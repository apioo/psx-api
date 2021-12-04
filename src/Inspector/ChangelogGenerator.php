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

namespace PSX\Api\Inspector;

use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Inspector\ChangelogGenerator as SchemaChangelogGenerator;
use PSX\Schema\Inspector\SemVer;

/**
 * ChangelogGenerator
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ChangelogGenerator
{
    public function generate(SpecificationInterface $left, ?SpecificationInterface $right = null)
    {
        if (empty($right)) {
            yield SemVer::PATCH => 'Initial release';
            return;
        }

        yield from $this->generateCollection($left->getResourceCollection(), $right->getResourceCollection());
        yield from (new SchemaChangelogGenerator())->generate($left->getDefinitions(), $right->getDefinitions());
    }

    private function generateCollection(ResourceCollection $left, ResourceCollection $right): \Generator
    {
        foreach ($left as $path => $resource) {
            if ($right->offsetExists($path)) {
                yield from $this->generateResource($resource, $right->offsetGet($path), $path);
            } else {
                yield SemVer::MAJOR => $this->getMessageRemoved([$path]);
            }
        }

        foreach ($right as $path => $resource) {
            if (!$left->offsetExists($path)) {
                yield SemVer::PATCH => $this->getMessageAdded([$path]);
            }
        }
    }

    private function generateResource(Resource $left, Resource $right, string $path): \Generator
    {
        foreach ($left->getMethods() as $methodName => $method) {
            if (isset($right[$methodName])) {
                yield from $this->generateMethod($method, $right[$methodName], $path, $methodName);
            } else {
                yield SemVer::MAJOR => $this->getMessageRemoved([$path, $methodName]);
            }
        }

        foreach ($right->getMethods() as $methodName => $method) {
            if (!isset($left[$methodName])) {
                yield SemVer::PATCH => $this->getMessageAdded([$path, $methodName]);
            }
        }
    }

    private function generateMethod(Resource\MethodAbstract $left, Resource\MethodAbstract $right, string $path, string $methodName): \Generator
    {
        if ($left->getOperationId() !== $right->getOperationId()) {
            yield SemVer::MINOR => $this->getMessageChanged([$path, $methodName, 'operationId'], $left->getOperationId(), $right->getOperationId());
        }

        if ($left->getDescription() !== $right->getDescription()) {
            yield SemVer::PATCH => $this->getMessageChanged([$path, $methodName, 'description'], $left->getDescription(), $right->getDescription());
        }

        if ($left->getQueryParameters() !== $right->getQueryParameters()) {
            yield SemVer::MINOR => $this->getMessageChanged([$path, $methodName, 'queryParameters'], $left->getQueryParameters(), $right->getQueryParameters());
        }

        if ($left->getRequest() !== $right->getRequest()) {
            yield SemVer::MINOR => $this->getMessageChanged([$path, $methodName, 'request'], $left->getRequest(), $right->getRequest());
        }

        if ($left->getResponses() !== $right->getResponses()) {
            foreach ($left->getResponses() as $statusCode => $response) {
                yield SemVer::MINOR => $this->getMessageChanged([$path, $methodName, $statusCode], $left->getRequest(), $right->getRequest());
            }
        }
    }

    private function getMessageAdded(array $path): string
    {
        $type = count($path) === 1 ? 'Resource' : 'Method';
        return $type . ' "' . implode('.', $path) . '" was added';
    }

    private function getMessageRemoved(array $path): string
    {
        $type = count($path) === 1 ? 'Resource' : 'Method';
        return $type . ' "' . implode('.', $path) . '" was removed';
    }

    private function getMessageChanged(array $path, $from, $to): string
    {
        $from = $from ?? 'NULL';
        $to = $to ?? 'NULL';

        return 'Property "' . implode('.', $path) . '" has changed from "' . $from . '" to "' . $to . '"';
    }
}
