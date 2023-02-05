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

use PSX\Schema\TypeInterface;

/**
 * Argument
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Argument
{
    private string $in;
    private TypeInterface $type;

    public function __construct(string $in, TypeInterface $type)
    {
        if (!in_array($in, ['path', 'query', 'body'])) {
            throw new \InvalidArgumentException('Provided an invalid "in" value, must be path, query or body');
        }

        $this->in = $in;
        $this->type = $type;
    }

    public function getIn(): int
    {
        return $this->in;
    }

    public function getType(): TypeInterface
    {
        return $this->type;
    }
}
