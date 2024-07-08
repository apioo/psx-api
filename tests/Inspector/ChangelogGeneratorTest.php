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
        $left = $this->apiManager->getApi(__DIR__ . '/resource/left.json');
        $right = $this->apiManager->getApi(__DIR__ . '/resource/right.json');

        $generator = new ChangelogGenerator();

        $actual = iterator_to_array($generator->generate($left, $right), false);
        $expect = [
            'Specification "baseUrl" has changed from "https://api.acme.com" to "https://api.foobar.com"',
            'Security "type" has changed from "httpBearer" to "oauth2"',
            'Security "tokenUrl" was added',
            'Security "scopes" was added',
            'Operation "my.operation.get.method" has changed from "GET" to "PUT"',
            'Operation "my.operation.get.path" has changed from "/my/endpoint" to "/my/endpoint/foo"',
            'Operation "my.operation.get.return.code" has changed from "200" to "201"',
            'Type "my.operation.get.return" (reference) ref has changed from "Person" to "Message"',
            'Operation "my.operation.get.arguments.id.in" has changed from "path" to "query"',
            'Type "my.operation.get.arguments.id" (string) type has changed from "string" to "integer"',
            'Operation "my.operation.get.arguments.search" was removed',
            'Operation "my.operation.get.arguments.payload" was added',
            'Type "my.operation.get.throws.500" (reference) ref has changed from "Message" to "Person"',
            'Operation "my.operation.get.throws.400" was added',
            'Operation "my.operation.get.description" has changed from "And a great description" to "And a great description foo"',
            'Operation "my.operation.get.stability" has changed from "1" to "2"',
            'Operation "my.operation.get.security" has changed from "" to "foo"',
            'Operation "my.operation.get.authorization" has changed from "1" to ""',
            'Operation "my.operation.get.tags" has changed from "" to "bar"',
            'Operation "my.operation.delete" was removed',
            'Operation "my.operation.execute" was added',
            'Property "Person.firstName" (string) description has changed from "" to "foobar"',
            'Property "Person.lastName" (string) type has changed from "string" to "integer"',
            'Property "Person.age" was added',
            'Type "Message" was added',
        ];

        $this->assertEquals($expect, $actual);
    }

    public function testGenerateReverse()
    {
        $left = $this->apiManager->getApi(__DIR__ . '/resource/left.json');
        $right = $this->apiManager->getApi(__DIR__ . '/resource/right.json');

        $generator = new ChangelogGenerator();

        $actual = iterator_to_array($generator->generate($right, $left), false);
        $expect = [
            'Specification "baseUrl" has changed from "https://api.foobar.com" to "https://api.acme.com"',
            'Security "type" has changed from "oauth2" to "httpBearer"',
            'Security "tokenUrl" was removed',
            'Security "scopes" was removed',
            'Operation "my.operation.get.method" has changed from "PUT" to "GET"',
            'Operation "my.operation.get.path" has changed from "/my/endpoint/foo" to "/my/endpoint"',
            'Operation "my.operation.get.return.code" has changed from "201" to "200"',
            'Type "my.operation.get.return" (reference) ref has changed from "Message" to "Person"',
            'Operation "my.operation.get.arguments.id.in" has changed from "query" to "path"',
            'Type "my.operation.get.arguments.id" (integer) type has changed from "integer" to "string"',
            'Operation "my.operation.get.arguments.payload" was removed',
            'Operation "my.operation.get.arguments.search" was added',
            'Type "my.operation.get.throws.500" (reference) ref has changed from "Person" to "Message"',
            'Operation "my.operation.get.throws.400" was removed',
            'Operation "my.operation.get.description" has changed from "And a great description foo" to "And a great description"',
            'Operation "my.operation.get.stability" has changed from "2" to "1"',
            'Operation "my.operation.get.security" has changed from "foo" to ""',
            'Operation "my.operation.get.authorization" has changed from "" to "1"',
            'Operation "my.operation.get.tags" has changed from "bar" to ""',
            'Operation "my.operation.execute" was removed',
            'Operation "my.operation.delete" was added',
            'Property "Person.firstName" (string) description has changed from "foobar" to ""',
            'Property "Person.lastName" (integer) type has changed from "integer" to "string"',
            'Property "Person.age" was removed',
            'Type "Message" was removed',
        ];

        $this->assertEquals($expect, $actual);
    }
}
