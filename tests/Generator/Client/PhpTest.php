<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\Exception\InvalidTypeException;
use PSX\Api\Generator\Client\Php;
use PSX\Api\Tests\Generator\GeneratorTestCase;
use PSX\Schema\Generator\Config;

/**
 * PhpTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class PhpTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new Php('http://api.foo.com');

        $result = $generator->generate($this->getSpecification());
        $target = __DIR__ . '/resource/php';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.php');
    }

    public function testGenerateCollection()
    {
        $generator = new Php('http://api.foo.com', Config::of('Foo\\Bar'));

        $result = $generator->generate($this->getSpecificationCollection());
        $target = __DIR__ . '/resource/php_collection';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.php');
    }

    public function testGenerateComplex()
    {
        $this->expectException(InvalidTypeException::class);

        $generator = new Php('http://api.foo.com', Config::of('Foo\\Bar'));
        $generator->generate($this->getSpecificationComplex());
    }

    public function testGenerateTest()
    {
        $generator = new Php('http://127.0.0.1:8081', Config::of('Sdkgen\\Client\\Tests\\Generated'));

        $result = $generator->generate($this->getSpecificationTest());
        $target = __DIR__ . '/resource/php_test';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.php');
    }
}
