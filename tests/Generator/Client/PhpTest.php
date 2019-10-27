<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

        $actual = (string) $generator->generate($this->getResource());
        $actual = str_replace(date('Y-m-d'), '0000-00-00', $actual);
        $expect = file_get_contents(__DIR__ . '/resource/php.php');
        $expect = str_replace(["\r\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateComplex()
    {
        $generator = new Php('http://api.foo.com', 'Foo\Bar');

        $actual = (string) $generator->generate($this->getResourceComplex());
        $actual = str_replace(date('Y-m-d'), '0000-00-00', $actual);
        $expect = file_get_contents(__DIR__ . '/resource/php_complex.php');
        $expect = str_replace(["\r\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }
}
