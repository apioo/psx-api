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

namespace PSX\Api\Operation;

use PSX\Schema\ContentType;
use PSX\Schema\TypeInterface;

/**
 * Response
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Response implements ResponseInterface, \JsonSerializable
{
    private int $code;
    private TypeInterface|ContentType $schema;

    public function __construct(int $code, TypeInterface|ContentType $schema)
    {
        $this->code = $code;
        $this->schema = $schema;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getSchema(): TypeInterface|ContentType
    {
        return $this->schema;
    }

    public function jsonSerialize(): array
    {
        if ($this->schema instanceof ContentType) {
            $contentType = $this->schema->value;
            $schema = null;
        } else {
            $contentType = null;
            $schema = $this->schema;
        }

        return array_filter([
            'code' => $this->code,
            'contentType' => $contentType,
            'schema' => $schema,
        ], fn ($value) => $value !== null);
    }
}
