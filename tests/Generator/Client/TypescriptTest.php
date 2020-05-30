<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2019 Christoph Kappestein <christoph.kappestein@gmail.com>
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

        $result = $generator->generate($this->getResource());
        $target = __DIR__ . '/resource/typescript';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Collection.ts');
        $this->assertFileExists($target . '/FooNameTypeResource.ts');
        $this->assertFileExists($target . '/GetQuery.ts');
        $this->assertFileExists($target . '/Item.ts');
        $this->assertFileExists($target . '/ItemCreate.ts');
        $this->assertFileExists($target . '/ItemPatch.ts');
        $this->assertFileExists($target . '/ItemUpdate.ts');
        $this->assertFileExists($target . '/Message.ts');
        $this->assertFileExists($target . '/Path.ts');
    }

    public function testGenerateCollection()
    {
        $generator = new Typescript('http://api.foo.com');

        $result = $generator->generateAll($this->getResourceCollection());
        $target = __DIR__ . '/resource/typescript_collection';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/BarFooResource.ts');
        $this->assertFileExists($target . '/BarYear09Resource.ts');
        $this->assertFileExists($target . '/Collection.ts');
        $this->assertFileExists($target . '/FooResource.ts');
        $this->assertFileExists($target . '/Item.ts');
        $this->assertFileExists($target . '/ItemCreate.ts');
        $this->assertFileExists($target . '/Message.ts');
        $this->assertFileExists($target . '/Path.ts');
    }

    public function testGenerateComplex()
    {
        $generator = new Typescript('http://api.foo.com');

        $result = $generator->generate($this->getResourceComplex());
        $target = __DIR__ . '/resource/typescript_complex';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/FooNameTypeResource.ts');
        $this->assertFileExists($target . '/Item.ts');
        $this->assertFileExists($target . '/Message.ts');
        $this->assertFileExists($target . '/Path.ts');
    }
}
