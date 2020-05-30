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

use PSX\Api\Generator\Client\Php;
use PSX\Api\Tests\Generator\GeneratorTestCase;
use PSX\Schema\Generator\Code\Chunks;

/**
 * PhpTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PhpTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new Php('http://api.foo.com');

        $result = $generator->generate($this->getResource());
        $target = __DIR__ . '/resource/php';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Collection.php');
        $this->assertFileExists($target . '/FooNameTypeResource.php');
        $this->assertFileExists($target . '/GetQuery.php');
        $this->assertFileExists($target . '/Item.php');
        $this->assertFileExists($target . '/ItemCreate.php');
        $this->assertFileExists($target . '/ItemPatch.php');
        $this->assertFileExists($target . '/ItemUpdate.php');
        $this->assertFileExists($target . '/Message.php');
        $this->assertFileExists($target . '/Path.php');
    }

    public function testGenerateCollection()
    {
        $generator = new Php('http://api.foo.com', 'Foo\Bar');

        $result = $generator->generateAll($this->getResourceCollection());
        $target = __DIR__ . '/resource/php_collection';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/BarFooResource.php');
        $this->assertFileExists($target . '/BarYear09Resource.php');
        $this->assertFileExists($target . '/Collection.php');
        $this->assertFileExists($target . '/FooResource.php');
        $this->assertFileExists($target . '/Item.php');
        $this->assertFileExists($target . '/ItemCreate.php');
        $this->assertFileExists($target . '/Message.php');
        $this->assertFileExists($target . '/Path.php');
    }

    public function testGenerateComplex()
    {
        $generator = new Php('http://api.foo.com', 'Foo\Bar');

        $result = $generator->generate($this->getResourceComplex());
        $target = __DIR__ . '/resource/php_complex';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/FooNameTypeResource.php');
        $this->assertFileExists($target . '/Item.php');
        $this->assertFileExists($target . '/Message.php');
        $this->assertFileExists($target . '/Path.php');
    }
}
