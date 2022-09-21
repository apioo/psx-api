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

    public function buildClassNameByPath(string $path): string
    {
        $parts = explode('/', $path);

        $result = [];
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

        $result[] = 'Resource';

        return $this->normalizer->class(...$result);
    }

    public function buildClassNameByTag(string $tag): string
    {
        $className = str_replace(['.', ' '], '_', $tag);

        return $this->normalizer->class($className, 'Group');
    }

    public function buildResourceGetter(string $className): string
    {
        $methodName = substr($className, 0, -8);

        return $this->normalizer->method('get', $methodName);
    }

    public function buildTagGetter(string $methodName): string
    {
        $methodName = str_replace(['.', ' '], '_', $methodName);

        return $this->normalizer->method($methodName);
    }
}
