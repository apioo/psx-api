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

namespace PSX\Api\Tests\Parser\Attribute;

use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\QueryParam;

/**
 * TestController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
#[Description('Test description')]
#[Path('/foo')]
#[PathParam(name: 'fooId', type: 'string', required: true)]
class TestController
{
    #[Description('file://' . __DIR__ . '/description.md')]
    #[QueryParam(name: "foo", type: "string", description: "Test")]
    #[QueryParam(name: "bar", type: "string", required: true)]
    #[QueryParam(name: "baz", type: "string", enum: ["foo", "bar"])]
    #[QueryParam(name: "boz", type: "string", pattern: "[A-z]+")]
    #[QueryParam(name: "integer", type: "integer")]
    #[QueryParam(name: "number", type: "number")]
    #[QueryParam(name: "date", type: "date")]
    #[QueryParam(name: "boolean", type: "boolean")]
    #[QueryParam(name: "string", type: "string")]
    #[Incoming(schema: __DIR__ . "/../schema/schema.json")]
    #[Outgoing(code: 200, schema: __DIR__ . "/../schema/schema.json")]
    protected function doGet()
    {
    }
}
