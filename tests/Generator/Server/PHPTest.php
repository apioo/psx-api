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

namespace PSX\Api\Tests\Generator\Server;

use PSX\Api\Generator\Server\PHP;
use PSX\Api\Tests\Generator\GeneratorTestCase;
use PSX\Schema\Generator\Config;

/**
 * PHPTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class PHPTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new PHP();

        $result = $generator->generate($this->getSpecification());
        $target = __DIR__ . '/resource/php';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Controller/App.php');
        $this->assertFileExists($target . '/Model/Entry.php');
    }

    public function testGenerateCollection()
    {
        $generator = new PHP(config: Config::of('My\\App'));

        $result = $generator->generate($this->getSpecificationCollection());
        $target = __DIR__ . '/resource/php_complex';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Controller/Foo/Bar.php');
        $this->assertFileExists($target . '/Controller/Foo/Baz.php');
        $this->assertFileExists($target . '/Controller/Bar.php');
        $this->assertFileExists($target . '/Model/Entry.php');
    }

    public function testGenerateContentType()
    {
        $generator = new PHP();

        $result = $generator->generate($this->getSpecificationContentType());
        $target = __DIR__ . '/resource/php_content_type';

        $this->writeChunksToFolder($result, $target);

        $this->assertFileExists($target . '/Controller/App.php');
    }
}
