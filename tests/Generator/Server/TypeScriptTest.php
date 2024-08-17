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

namespace PSX\Api\Tests\Generator\Server;

use PSX\Api\Generator\Server\TypeScript;
use PSX\Api\Tests\Generator\GeneratorTestCase;

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
        $generator = new TypeScript();

        $result = $generator->generate($this->getSpecification());
        $target = __DIR__ . '/resource/typescript';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/src/controller/app.controller.ts');
        $this->assertFileExists($target . '/src/dto/Entry.ts');
    }

    public function testGenerateCollection()
    {
        $generator = new TypeScript();

        $result = $generator->generate($this->getSpecificationCollection());
        $target = __DIR__ . '/resource/typescript_complex';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/src/controller/foo/bar.controller.ts');
        $this->assertFileExists($target . '/src/controller/foo/baz.controller.ts');
        $this->assertFileExists($target . '/src/controller/bar.controller.ts');
        $this->assertFileExists($target . '/src/dto/Entry.ts');
    }
}
