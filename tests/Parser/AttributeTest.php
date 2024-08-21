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

namespace PSX\Api\Tests\Parser;

use PSX\Api\Exception\ParserException;
use PSX\Api\OperationInterface;
use PSX\Api\Parser\Attribute as AttributeParser;
use PSX\Api\SpecificationInterface;
use PSX\Api\Tests\Parser\Attribute\BarController;
use PSX\Api\Tests\Parser\Attribute\PropertyController;
use PSX\Api\Tests\Parser\Attribute\TestController;
use PSX\Schema\TypeFactory;

/**
 * AttributeTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class AttributeTest extends ParserTestCase
{
    /**
     * @inheritDoc
     */
    protected function getSpecification(): SpecificationInterface
    {
        return $this->apiManager->getApi(TestController::class);
    }

    public function testOperationId()
    {
        $specification = $this->apiManager->getApi(TestController::class);
        $operation = $specification->getOperations()->get('PSX.Api.Tests.Parser.Attribute.TestController.doGet');

        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals('A long **Test** description', $operation->getDescription());
    }

    public function testParseTypeHint()
    {
        $annotation = new AttributeParser($this->schemaManager);
        $specification = $annotation->parse(BarController::class);
        $operation = $specification->getOperations()->get('PSX.Api.Tests.Parser.Attribute.BarController.myMethod');

        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals('path', $operation->getArguments()->get('id')->getIn());
        $this->assertEquals(TypeFactory::getInteger(), $operation->getArguments()->get('id')->getSchema());
        $this->assertEquals('query', $operation->getArguments()->get('year')->getIn());
        $this->assertEquals(TypeFactory::getString(), $operation->getArguments()->get('year')->getSchema());
        $this->assertEquals('body', $operation->getArguments()->get('incoming')->getIn());
        $this->assertEquals(TypeFactory::getReference('Incoming'), $operation->getArguments()->get('incoming')->getSchema());
        $this->assertEquals(200, $operation->getReturn()->getCode());
        $this->assertEquals(TypeFactory::getReference('Outgoing'), $operation->getReturn()->getSchema());
    }

    public function testParseInvalid()
    {
        $this->expectException(ParserException::class);

        $annotation = new AttributeParser($this->schemaManager);
        $annotation->parse('foo');
    }

    public function testParseProperty()
    {
        $specification = $this->apiManager->getApi(PropertyController::class);
        $operation = $specification->getOperations()->get('PSX.Api.Tests.Parser.Attribute.PropertyController.doGet');

        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals('Test description', $operation->getDescription());
    }
}
