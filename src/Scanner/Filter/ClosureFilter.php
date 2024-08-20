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

namespace PSX\Api\Scanner\Filter;

use PSX\Api\OperationInterface;
use PSX\Api\Scanner\FilterInterface;

/**
 * ClosureFilter
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ClosureFilter implements FilterInterface
{
    private \Closure $closure;

    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    public function match(OperationInterface $operation): bool
    {
        return call_user_func($this->closure, $operation) === true;
    }

    public function getId(): string
    {
        return substr(md5((string) new \ReflectionFunction($this->closure)), 0, 8);
    }
}
