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

namespace PSX\Api\Tests\Parser;

use PSX\Api\OperationInterface;
use PSX\Api\SpecificationInterface;
use PSX\Api\Tests\ApiManagerTestCase;
use PSX\Schema\Format;
use PSX\Schema\TypeFactory;

/**
 * ParserTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class ParserTestCase extends ApiManagerTestCase
{
    public function testParseSimple()
    {
        $specification = $this->getSpecification();
        $definitions = $specification->getDefinitions();
        $operation = $specification->getOperations()->get('PSX.Api.Tests.Parser.Attribute.TestController.doGet');

        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals('GET', $operation->getMethod());
        $this->assertEquals('/foo/:fooId', $operation->getPath());
        $this->assertEquals('A long **Test** description', $operation->getDescription());
        $this->assertEquals(['foo'], $operation->getTags());

        $arguments = $operation->getArguments();
        $this->assertEquals('path', $arguments->get('fooId')->getIn());
        $this->assertEquals(['type' => 'string'], $arguments->get('fooId')->getSchema()->toArray());
        $this->assertEquals('query', $arguments->get('foo')->getIn());
        $this->assertEquals(['type' => 'string', 'description' => 'Test'], $arguments->get('foo')->getSchema()->toArray());
        $this->assertEquals('query', $arguments->get('bar')->getIn());
        $this->assertEquals(['type' => 'string'], $arguments->get('bar')->getSchema()->toArray());
        $this->assertEquals('query', $arguments->get('baz')->getIn());
        $this->assertEquals(['type' => 'string', 'enum' => ['foo', 'bar']], $arguments->get('baz')->getSchema()->toArray());
        $this->assertEquals('query', $arguments->get('boz')->getIn());
        $this->assertEquals(['type' => 'string', 'pattern' => '[A-z]+'], $arguments->get('boz')->getSchema()->toArray());
        $this->assertEquals('query', $arguments->get('integer')->getIn());
        $this->assertEquals(['type' => 'integer'], $arguments->get('integer')->getSchema()->toArray());
        $this->assertEquals('query', $arguments->get('number')->getIn());
        $this->assertEquals(['type' => 'number'], $arguments->get('number')->getSchema()->toArray());
        $this->assertEquals('query', $arguments->get('date')->getIn());
        $this->assertEquals(['type' => 'string', 'format' => Format::DATETIME], $arguments->get('date')->getSchema()->toArray());
        $this->assertEquals('query', $arguments->get('boolean')->getIn());
        $this->assertEquals(['type' => 'boolean'], $arguments->get('boolean')->getSchema()->toArray());
        $this->assertEquals('query', $arguments->get('string')->getIn());
        $this->assertEquals(['type' => 'string'], $arguments->get('string')->getSchema()->toArray());
        $this->assertEquals('body', $arguments->get('payload')->getIn());
        $this->assertEquals(['$ref' => 'Song'], $arguments->get('payload')->getSchema()->toArray());

        $this->assertEquals(200, $operation->getReturn()->getCode());
        $this->assertEquals(['$ref' => 'Song'], $operation->getReturn()->getSchema()->toArray());

        $this->assertCount(1, $operation->getThrows());
        $this->assertEquals(500, $operation->getThrows()[0]->getCode());
        $this->assertEquals(['$ref' => 'Error'], $operation->getThrows()[0]->getSchema()->toArray());

        $this->assertEquals([
            'description' => 'A canonical song',
            'type' => 'object',
            'properties' => [
                'title' => TypeFactory::getString(),
                'artist' => TypeFactory::getString(),
                'length' => TypeFactory::getInteger(),
                'ratings' => TypeFactory::getArray(TypeFactory::getReference('Rating')),
            ],
            'required' => ['title', 'artist']
        ], $definitions->getType('Song')->toArray());
        $this->assertEquals([
            'type' => 'object',
            'properties' => [
                'success' => TypeFactory::getBoolean(),
                'message' => TypeFactory::getString(),
            ],
        ], $definitions->getType('Error')->toArray());
    }

    /**
     * @return \PSX\Api\SpecificationInterface
     */
    abstract protected function getSpecification(): SpecificationInterface;
}
