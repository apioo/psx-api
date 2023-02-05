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
 * HttpResponse
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Response
{
    private int $code;
    private TypeInterface $schema;

    public function __construct(int $code, TypeInterface $schema)
    {
        if (!($code >= 200 && $code < 600)) {
            throw new \InvalidArgumentException('Provided an invalid "code" value, must be a valid HTTP status code');
        }

        $this->code = $code;
        $this->schema = $schema;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getSchema(): TypeInterface
    {
        return $this->schema;
    }
}
