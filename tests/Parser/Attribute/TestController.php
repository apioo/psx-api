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

namespace PSX\Api\Tests\Parser\Attribute;

use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\QueryParam;
use PSX\Api\Attribute\Tags;
use PSX\DateTime\DateTime;
use PSX\Schema\Format;
use PSX\Schema\Type;

/**
 * TestController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[Description('Test description')]
#[Path('/foo/:fooId')]
#[PathParam(name: 'fooId', type: Type::STRING, required: true)]
class TestController
{
    #[Get]
    #[Description('file://' . __DIR__ . '/description.md')]
    #[QueryParam(name: "foo", type: Type::STRING, description: "Test")]
    #[QueryParam(name: "bar", type: Type::STRING, required: true)]
    #[QueryParam(name: "baz", type: Type::STRING, enum: ["foo", "bar"])]
    #[QueryParam(name: "boz", type: Type::STRING, pattern: "[A-z]+")]
    #[QueryParam(name: "integer", type: Type::INTEGER)]
    #[QueryParam(name: "number", type: Type::NUMBER)]
    #[QueryParam(name: "date", type: Type::STRING, format: Format::DATETIME)]
    #[QueryParam(name: "boolean", type: Type::BOOLEAN)]
    #[QueryParam(name: "string", type: Type::STRING)]
    #[Incoming(schema: __DIR__ . "/../schema/schema.json")]
    #[Outgoing(code: 200, schema: __DIR__ . "/../schema/schema.json")]
    #[Outgoing(code: 500, schema: __DIR__ . "/../schema/error.json")]
    #[Tags(['foo'])]
    protected function doGet(string $fooId, string $foo, string $bar, string $baz, string $boz, int $integer, float $number, DateTime $date, bool $boolean, string $string)
    {
    }
}
