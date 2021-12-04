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

use PSX\Api\Generator\Client\Go;
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

        $result = $generator->generate($this->getSpecification());
        $target = __DIR__ . '/resource/go';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/entry.go');
        $this->assertFileExists($target . '/entry_collection.go');
        $this->assertFileExists($target . '/entry_create.go');
        $this->assertFileExists($target . '/entry_delete.go');
        $this->assertFileExists($target . '/entry_message.go');
        $this->assertFileExists($target . '/entry_patch.go');
        $this->assertFileExists($target . '/entry_update.go');
        $this->assertFileExists($target . '/foo_by_name_and_type_resource.go');
        $this->assertFileExists($target . '/get_query.go');
        $this->assertFileExists($target . '/path.go');
    }

    public function testGenerateCollection()
    {
        $generator = new Go('http://api.foo.com');

        $result = $generator->generate($this->getSpecificationCollection());
        $target = __DIR__ . '/resource/go_collection';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/bar_by_foo_resource.go');
        $this->assertFileExists($target . '/bar_by_year_resource.go');
        $this->assertFileExists($target . '/entry.go');
        $this->assertFileExists($target . '/entry_collection.go');
        $this->assertFileExists($target . '/entry_create.go');
        $this->assertFileExists($target . '/entry_message.go');
        $this->assertFileExists($target . '/foo_resource.go');
        $this->assertFileExists($target . '/path_foo.go');
        $this->assertFileExists($target . '/path_year.go');
    }

    public function testGenerateComplex()
    {
        $generator = new Go('http://api.foo.com');

        $result = $generator->generate($this->getSpecificationComplex());
        $target = __DIR__ . '/resource/go_complex';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/entry.go');
        $this->assertFileExists($target . '/entry_message.go');
        $this->assertFileExists($target . '/foo_by_name_and_type_resource.go');
        $this->assertFileExists($target . '/path.go');
    }
}
