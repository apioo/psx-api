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

namespace PSX\Api\Tests\Generator\Client;

use PSX\Api\Generator\Client\Typescript;
use PSX\Api\Tests\Generator\GeneratorTestCase;

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
        $generator = new Typescript('http://api.foo.com');

        $result = $generator->generate($this->getSpecification());
        $target = __DIR__ . '/resource/typescript';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.ts');
        $this->assertFileExists($target . '/Entry.ts');
        $this->assertFileExists($target . '/EntryCollection.ts');
        $this->assertFileExists($target . '/EntryCreate.ts');
        $this->assertFileExists($target . '/EntryDelete.ts');
        $this->assertFileExists($target . '/EntryMessage.ts');
        $this->assertFileExists($target . '/EntryPatch.ts');
        $this->assertFileExists($target . '/EntryUpdate.ts');
        $this->assertFileExists($target . '/FooByNameAndTypeResource.ts');
        $this->assertFileExists($target . '/GetQuery.ts');
        $this->assertFileExists($target . '/Path.ts');
    }

    public function testGenerateCollection()
    {
        $generator = new Typescript('http://api.foo.com');

        $result = $generator->generate($this->getSpecificationCollection());
        $target = __DIR__ . '/resource/typescript_collection';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/BarByFooResource.ts');
        $this->assertFileExists($target . '/BarByYearResource.ts');
        $this->assertFileExists($target . '/Client.ts');
        $this->assertFileExists($target . '/Entry.ts');
        $this->assertFileExists($target . '/EntryCollection.ts');
        $this->assertFileExists($target . '/EntryCreate.ts');
        $this->assertFileExists($target . '/EntryMessage.ts');
        $this->assertFileExists($target . '/FooResource.ts');
        $this->assertFileExists($target . '/PathFoo.ts');
        $this->assertFileExists($target . '/PathYear.ts');
    }

    public function testGenerateComplex()
    {
        $generator = new Typescript('http://api.foo.com');

        $result = $generator->generate($this->getSpecificationComplex());
        $target = __DIR__ . '/resource/typescript_complex';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.ts');
        $this->assertFileExists($target . '/Entry.ts');
        $this->assertFileExists($target . '/EntryMessage.ts');
        $this->assertFileExists($target . '/EntryOrMessage.ts');
        $this->assertFileExists($target . '/FooByNameAndTypeResource.ts');
        $this->assertFileExists($target . '/Path.ts');
    }
}
