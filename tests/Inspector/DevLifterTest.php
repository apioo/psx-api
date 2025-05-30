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

namespace PSX\Api\Tests\Inspector;

use PSX\Api\Inspector\ChangelogGenerator;
use PSX\Api\Inspector\DevLifter;
use PSX\Api\Tests\Generator\GeneratorTestCase;

/**
 * DevLifterTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class DevLifterTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $left = $this->apiManager->getApi(__DIR__ . '/resource/left.json');
        $right = $this->apiManager->getApi(__DIR__ . '/resource/right.json');

        $lifter = new DevLifter();

        $actual = $lifter->elevate('0.1.0', $left, $right);
        $expect = '0.2.0';

        $this->assertEquals($expect, $actual);
    }
}
