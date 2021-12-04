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

namespace PSX\Api\Tests\Inspector;

use PSX\Api\Inspector\ChangelogGenerator;
use PSX\Api\Tests\Generator\GeneratorTestCase;

/**
 * ChangelogGeneratorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ChangelogGeneratorTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $left = $this->getSpecification();
        $right = $this->getSpecificationCollection();

        $generator = new ChangelogGenerator();

        $actual = iterator_to_array($generator->generate($left, $right), false);
        $expect = [
            'Resource "/foo/:name/:type" was removed',
            'Resource "/foo" was added',
            'Resource "/bar/:foo" was added',
            'Resource "/bar/$year<[0-9]+>" was added',
            'Type "Path" was removed',
            'Type "GetQuery" was removed',
            'Type "EntryUpdate" was removed',
            'Type "EntryDelete" was removed',
            'Type "EntryPatch" was removed',
            'Type "PathFoo" was added',
            'Type "PathYear" was added',
        ];

        $this->assertEquals($expect, $actual);
    }
}
