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

use PSX\Api\Generator\Client\Go;
use PSX\Api\Generator\Client\Typescript;
use PSX\Api\Tests\Generator\GeneratorTestCase;

/**
 * GoTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GoTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new Go('http://api.foo.com');

        $result = $generator->generate($this->getResource());
        $target = __DIR__ . '/resource/go';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/collection.go');
        $this->assertFileExists($target . '/foo_name_type_resource.go');
        $this->assertFileExists($target . '/get_query.go');
        $this->assertFileExists($target . '/item.go');
        $this->assertFileExists($target . '/item_create.go');
        $this->assertFileExists($target . '/item_patch.go');
        $this->assertFileExists($target . '/item_update.go');
        $this->assertFileExists($target . '/message.go');
        $this->assertFileExists($target . '/path.go');
    }

    public function testGenerateCollection()
    {
        $generator = new Go('http://api.foo.com');

        $result = $generator->generateAll($this->getResourceCollection());
        $target = __DIR__ . '/resource/go_collection';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/bar_foo_resource.go');
        $this->assertFileExists($target . '/bar_year09_resource.go');
        $this->assertFileExists($target . '/collection.go');
        $this->assertFileExists($target . '/foo_resource.go');
        $this->assertFileExists($target . '/item.go');
        $this->assertFileExists($target . '/item_create.go');
        $this->assertFileExists($target . '/message.go');
        $this->assertFileExists($target . '/path.go');
    }

    public function testGenerateComplex()
    {
        $generator = new Go('http://api.foo.com');

        $result = $generator->generate($this->getResourceComplex());
        $target = __DIR__ . '/resource/go_complex';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/foo_name_type_resource.go');
        $this->assertFileExists($target . '/item.go');
        $this->assertFileExists($target . '/message.go');
        $this->assertFileExists($target . '/path.go');
    }
}
