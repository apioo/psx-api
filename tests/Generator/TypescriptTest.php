<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Tests\Generator;

use PSX\Api\Generator\Typescript;

/**
 * TypescriptTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TypescriptTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new Typescript();

        $actual = $generator->generate($this->getResource());
        $expect = file_get_contents(__DIR__ . '/resource/typescript.ts');
        $expect = str_replace(["\r\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateAll()
    {
        $generator = new Typescript();

        $actual = $generator->generateAll($this->getResourceCollection());
        $expect = file_get_contents(__DIR__ . '/resource/typescript_collection.ts');
        $expect = str_replace(["\r\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }
}