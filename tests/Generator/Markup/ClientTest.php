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

namespace PSX\Api\Tests\Generator\Markup;

use PSX\Api\Generator\Markup\Client;
use PSX\Api\Tests\Generator\GeneratorTestCase;

/**
 * ClientTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ClientTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new Client();

        $actual = $generator->generate($this->getSpecification());

        $expect = file_get_contents(__DIR__ . '/resource/client.md');
        $expect = str_replace(array("\r\n", "\r"), "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }
}
