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

namespace PSX\Api\Tests\Generator\Client;

use PSX\Api\Exception\InvalidTypeException;
use PSX\Api\Generator\Client\PHP;
use PSX\Api\Generator\Client\TypeScript;
use PSX\Api\Tests\Generator\GeneratorTestCase;
use PSX\Schema\Generator\Config;

/**
 * TypeScriptTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TypeScriptTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new TypeScript('http://api.foo.com');

        $result = $generator->generate($this->getSpecification());
        $target = __DIR__ . '/resource/typescript';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.ts');
    }

    public function testGenerateCollection()
    {
        $generator = new TypeScript('http://api.foo.com');

        $result = $generator->generate($this->getSpecificationCollection());
        $target = __DIR__ . '/resource/typescript_collection';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.ts');
    }

    public function testGenerateTest()
    {
        $generator = new TypeScript('http://127.0.0.1:8081');

        $result = $generator->generate($this->getSpecificationTest());
        $target = __DIR__ . '/resource/typescript_test';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.ts');
    }

    public function testGenerateContentType()
    {
        $generator = new TypeScript('http://api.foo.com');

        $result = $generator->generate($this->getSpecificationContentType());
        $target = __DIR__ . '/resource/typescript_content_type';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.ts');
    }

    public function testGenerateImport()
    {
        $generator = new TypeScript('http://api.foo.com', Config::of('', ['import' => '../foo']));

        $result = $generator->generate($this->getSpecificationImport());
        $target = __DIR__ . '/resource/typescript_import';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Client.ts');
    }
}
