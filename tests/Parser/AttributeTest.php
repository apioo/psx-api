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
use PSX\Api\Parser\Attribute\Builder;
use PSX\Api\SpecificationInterface;
use PSX\Api\Tests\Parser\Attribute\BarController;
use PSX\Api\Tests\Parser\Attribute\ContentTypeController;
use PSX\Api\Tests\Parser\Attribute\PropertyController;
use PSX\Api\Tests\Parser\Attribute\TestController;
use PSX\Schema\ContentType;
use PSX\Schema\Type\Factory\PropertyTypeFactory;
use PSX\Schema\TypeFactory;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

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
        $operation = $specification->getOperations()->get('test.get');

        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals('A long **Test** description', $operation->getDescription());
    }

    public function testParseTypeHint()
    {
        $annotation = new AttributeParser($this->schemaManager, new Builder(new ArrayAdapter(), false));
        $specification = $annotation->parse(BarController::class);
        $operation = $specification->getOperations()->get('tests.parser.attribute.bar_controller.myMethod');

        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals('path', $operation->getArguments()->get('id')->getIn());
        $this->assertEquals(PropertyTypeFactory::getInteger(), $operation->getArguments()->get('id')->getSchema());
        $this->assertEquals('query', $operation->getArguments()->get('year')->getIn());
        $this->assertEquals(PropertyTypeFactory::getString(), $operation->getArguments()->get('year')->getSchema());
        $this->assertEquals('body', $operation->getArguments()->get('incoming')->getIn());
        $this->assertEquals(PropertyTypeFactory::getReference('Incoming'), $operation->getArguments()->get('incoming')->getSchema());
        $this->assertEquals(200, $operation->getReturn()->getCode());
        $this->assertEquals(PropertyTypeFactory::getReference('Outgoing'), $operation->getReturn()->getSchema());
    }

    public function testParseInvalid()
    {
        $this->expectException(ParserException::class);

        $annotation = new AttributeParser($this->schemaManager, new Builder(new ArrayAdapter(), false));
        $annotation->parse('foo');
    }

    public function testParseProperty()
    {
        $specification = $this->apiManager->getApi(PropertyController::class);
        $operation = $specification->getOperations()->get('my.operation');

        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals('Test description', $operation->getDescription());
    }

    public function testParseContentType()
    {
        $specification = $this->apiManager->getApi(ContentTypeController::class);

        $operation = $specification->getOperations()->get('binary');
        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals(ContentType::BINARY, $operation->getArguments()->get('body')->getSchema());
        $this->assertEquals(ContentType::BINARY, $operation->getReturn()->getSchema());

        $operation = $specification->getOperations()->get('text');
        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals(ContentType::TEXT, $operation->getArguments()->get('body')->getSchema());
        $this->assertEquals(ContentType::TEXT, $operation->getReturn()->getSchema());

        $operation = $specification->getOperations()->get('form');
        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals(ContentType::FORM, $operation->getArguments()->get('body')->getSchema());
        $this->assertEquals(ContentType::FORM, $operation->getReturn()->getSchema());

        $operation = $specification->getOperations()->get('multipart');
        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals(ContentType::MULTIPART, $operation->getArguments()->get('body')->getSchema());
        $this->assertEquals(ContentType::MULTIPART, $operation->getReturn()->getSchema());

        $operation = $specification->getOperations()->get('json');
        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals(ContentType::JSON, $operation->getArguments()->get('body')->getSchema());
        $this->assertEquals(ContentType::JSON, $operation->getReturn()->getSchema());
    }
}
