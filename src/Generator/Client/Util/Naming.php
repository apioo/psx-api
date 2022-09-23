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

namespace PSX\Api\Generator\Client\Util;

use PSX\Api\Resource;
use PSX\Schema\Generator\Normalizer\NormalizerInterface;

/**
 * Naming
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Naming
{
    private NormalizerInterface $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function buildClassNameByResource(Resource $resource): string
    {
        $name = $resource->getName();
        if (!empty($name)) {
            $result = [$name];
        } else {
            $result = $this->buildNameByPath($resource->getPath());
        }

        $result[] = 'Resource';

        return $this->normalizer->class(...$result);
    }

    public function buildMethodNameByMethod(Resource\MethodAbstract $method): string
    {
        $operationId = $method->getOperationId();
        if (empty($operationId)) {
            $operationId = strtolower($method->getName());
        }

        return $this->normalizer->method($operationId);
    }

    public function buildResourceGetter(string $className): string
    {
        $methodName = substr($className, 0, -8);

        return $this->normalizer->method('get', $methodName);
    }

    private function buildNameByPath(string $path): array
    {
        $result = [];
        $parts = explode('/', $path);

        $i = 0;
        foreach ($parts as $part) {
            if (str_starts_with($part, ':')) {
                $part = ($i === 0 ? 'By' : 'And') . ucfirst(substr($part, 1));
                $i++;
            } elseif (str_starts_with($part, '$')) {
                $part = ($i === 0 ? 'By' : 'And') . ucfirst(substr($part, 1, strpos($part, '<')));
                $i++;
            }

            $result[] = $part;
        }

        return $result;
    }
}
