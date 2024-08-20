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

namespace PSX\Api\Tests\Generator\Spec;

use PSX\Api\Generator\Spec\TypeAPI;
use PSX\Api\Tests\Generator\GeneratorTestCase;

/**
 * TypeAPITest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TypeAPITest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new TypeAPI();

        $actual = $generator->generate($this->getSpecification());
        $expect = file_get_contents(__DIR__ . '/resource/typeapi.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGenerateAll()
    {
        $generator = new TypeAPI();

        $actual = $generator->generate($this->getSpecificationCollection());
        $expect = file_get_contents(__DIR__ . '/resource/typeapi_collection.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGenerateComplex()
    {
        $generator = new TypeAPI();

        $actual = $generator->generate($this->getSpecificationComplex());
        $expect = file_get_contents(__DIR__ . '/resource/typeapi_complex.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }
}
