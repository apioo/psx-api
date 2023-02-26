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

namespace PSX\Api\Tests\Inspector;

use PSX\Api\Inspector\ChangelogGenerator;
use PSX\Api\Tests\Generator\GeneratorTestCase;

/**
 * ChangelogGeneratorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
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
            'Operation "get" was removed',
            'Operation "create" was removed',
            'Operation "update" was removed',
            'Operation "delete" was removed',
            'Operation "patch" was removed',
            'Operation "foo.get" was added',
            'Operation "foo.create" was added',
            'Operation "bar.get" was added',
            'Operation "bar.create" was added',
            'Operation "baz.get" was added',
            'Operation "baz.create" was added',
            'Type "EntryUpdate" was removed',
            'Type "EntryDelete" was removed',
            'Type "EntryPatch" was removed',
        ];

        $this->assertEquals($expect, $actual);
    }
}
