<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\Generator\Client\Java;
use PSX\Api\Tests\Generator\GeneratorTestCase;

/**
 * JavaTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JavaTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new Java('http://api.foo.com');

        $result = $generator->generate($this->getSpecification());
        $target = __DIR__ . '/resource/java';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.java');
        $this->assertFileExists($target . '/Entry.java');
        $this->assertFileExists($target . '/EntryCollection.java');
        $this->assertFileExists($target . '/EntryCreate.java');
        $this->assertFileExists($target . '/EntryDelete.java');
        $this->assertFileExists($target . '/EntryMessage.java');
        $this->assertFileExists($target . '/EntryPatch.java');
        $this->assertFileExists($target . '/EntryUpdate.java');
        $this->assertFileExists($target . '/FooByNameAndTypeResource.java');
        $this->assertFileExists($target . '/GetQuery.java');
        $this->assertFileExists($target . '/Path.java');
    }

    public function testGenerateCollection()
    {
        $generator = new Java('http://api.foo.com');

        $result = $generator->generate($this->getSpecificationCollection());
        $target = __DIR__ . '/resource/java_collection';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/BarByFooResource.java');
        $this->assertFileExists($target . '/BarByYearResource.java');
        $this->assertFileExists($target . '/Client.java');
        $this->assertFileExists($target . '/Entry.java');
        $this->assertFileExists($target . '/EntryCollection.java');
        $this->assertFileExists($target . '/EntryCreate.java');
        $this->assertFileExists($target . '/EntryMessage.java');
        $this->assertFileExists($target . '/FooResource.java');
        $this->assertFileExists($target . '/PathFoo.java');
        $this->assertFileExists($target . '/PathYear.java');
    }

    public function testGenerateComplex()
    {
        $generator = new Java('http://api.foo.com');

        $result = $generator->generate($this->getSpecificationComplex());
        $target = __DIR__ . '/resource/java_complex';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.java');
        $this->assertFileExists($target . '/Entry.java');
        $this->assertFileExists($target . '/EntryMessage.java');
        $this->assertFileExists($target . '/EntryOrMessage.java');
        $this->assertFileExists($target . '/FooByNameAndTypeResource.java');
        $this->assertFileExists($target . '/Path.java');
    }
}
