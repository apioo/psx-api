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
 * Argument
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Argument implements ArgumentInterface, \JsonSerializable
{
    private string $in;
    private TypeInterface|ContentType $schema;
    private ?string $name;

    public function __construct(string $in, TypeInterface|ContentType $schema, ?string $name = null)
    {
        if (!in_array($in, [self::IN_PATH, self::IN_HEADER, self::IN_QUERY, self::IN_BODY])) {
            throw new \InvalidArgumentException('Provided an invalid "in" value, must be one of: ' . implode(', ', [self::IN_PATH, self::IN_HEADER, self::IN_QUERY, self::IN_BODY]));
        }

        $this->in = $in;
        $this->schema = $schema;
        $this->name = $name;
    }

    public function getIn(): string
    {
        return $this->in;
    }

    public function getSchema(): TypeInterface|ContentType
    {
        return $this->schema;
    }

    public function getName(): ?string
    {
        return $this->name;
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
            'in' => $this->in,
            'contentType' => $contentType,
            'schema' => $schema,
            'name' => $this->name,
        ], fn ($value) => $value !== null);
    }
}
