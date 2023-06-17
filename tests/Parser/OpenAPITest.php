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
use PSX\Schema\Format;
use PSX\Schema\TypeFactory;

/**
 * OpenAPITest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OpenAPITest extends ParserTestCase
{
    /**
     * @inheritDoc
     */
    protected function getSpecification(): SpecificationInterface
    {
        return $this->apiManager->getApi(__DIR__ . '/openapi/simple.json');
    }

    public function testParsePetstore()
    {
        $specification = $this->apiManager->getApi(__DIR__ . '/openapi/petstore.json');
        $definitions = $specification->getDefinitions();
        $operation = $specification->getOperations()->get('listPets');

        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals('GET', $operation->getMethod());
        $this->assertEquals('/pets', $operation->getPath());
        $this->assertEquals('List all pets', $operation->getDescription());

        $arguments = $operation->getArguments();
        $this->assertEquals('query', $arguments->get('limit')->getIn());
        $this->assertEquals(['type' => 'integer', 'format' => Format::INT32, 'maximum' => 100], $arguments->get('limit')->getSchema()->toArray());

        $this->assertEquals(200, $operation->getReturn()->getCode());
        $this->assertEquals(['$ref' => 'Pets'], $operation->getReturn()->getSchema()->toArray());

        $this->assertCount(0, $operation->getThrows());

        $this->assertEquals([
            'type' => 'array',
            'items' => TypeFactory::getReference('Pet'),
            'maxItems' => 100
        ], $definitions->getType('Pets')->toArray());
        $this->assertEquals([
            'type' => 'object',
            'properties' => [
                'id' => TypeFactory::getInteger()->setFormat(Format::INT64),
                'name' => TypeFactory::getString(),
                'tag' => TypeFactory::getString()
            ],
            'required' => ['id', 'name']
        ], $definitions->getType('Pet')->toArray());
    }

    public function testParseInline()
    {
        $specification = $this->apiManager->getApi(__DIR__ . '/openapi/inline.json');
        $definitions = $specification->getDefinitions();
        $operation = $specification->getOperations()->get('PSX.Api.Tests.Parser.Attribute.TestController.doGet');

        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals('GET', $operation->getMethod());
        $this->assertEquals('/foo', $operation->getPath());
        $this->assertEquals('', $operation->getDescription());

        $this->assertTrue($operation->getArguments()->isEmpty());

        $this->assertEquals(200, $operation->getReturn()->getCode());
        $this->assertEquals(['$ref' => 'Inline01fd4b61'], $operation->getReturn()->getSchema()->toArray());

        $this->assertCount(0, $operation->getThrows());

        $this->assertEquals([
            'type' => 'object',
            'properties' => [
                'success' => TypeFactory::getBoolean(),
                'message' => TypeFactory::getString(),
            ],
        ], $definitions->getType('Inline01fd4b61')->toArray());
    }

    /*
    public function testParseComplex()
    {
        $specification = OpenAPI::fromFile(__DIR__ . '/openapi/complex.json');

        $resource = $specification->getOperations()->get('/foo');
        $definitions = $specification->getDefinitions();

        $this->assertEquals('/foo', $resource->getPath());
        $this->assertEquals('Test description', $resource->getDescription());

        $path = $definitions->getType($resource->getPathParameters());

        $this->assertInstanceOf(StructType::class, $path);
        $this->assertInstanceOf(TypeInterface::class, $path->getProperty('fooId'));

        $methods = $resource->getMethods();

        $this->assertEquals(['GET'], array_keys($methods));

        $this->assertEquals('A long **Test** description', $methods['GET']->getDescription());

        $query = $definitions->getType($methods['GET']->getQueryParameters());

        $this->assertInstanceOf(StructType::class, $query);
        $this->assertInstanceOf(TypeInterface::class, $query->getProperty('bar'));

        $request = $definitions->getType($methods['GET']->getRequest());

        $this->assertInstanceOf(StructType::class, $request);
        $this->assertInstanceOf(TypeInterface::class, $request->getProperty('artist'));

        $response = $definitions->getType($methods['GET']->getResponse(200));

        $this->assertInstanceOf(StructType::class, $response);
        $this->assertInstanceOf(TypeInterface::class, $response->getProperty('artist'));

        $response = $definitions->getType($methods['GET']->getResponse(500));

        $this->assertInstanceOf(StructType::class, $response);
        $this->assertInstanceOf(TypeInterface::class, $response->getProperty('success'));
    }

    public function testParsePath()
    {
        $specification = OpenAPI::fromFile(__DIR__ . '/openapi/test.json', '/foo/:fooId');

        $this->assertInstanceOf(Resource::class, $specification->getOperations()->get('/foo/:fooId'));
    }

    public function testParseInvalidPath()
    {
        $specification = OpenAPI::fromFile(__DIR__ . '/openapi/test.json', '/test');

        $this->assertEquals(0, count($specification->getOperations()));
    }

    public function testParseAll()
    {
        $parser = new OpenAPI(__DIR__ . '/openapi');
        $specification = $parser->parse(file_get_contents(__DIR__ . '/openapi/simple.json'));

        $this->assertInstanceOf(ResourceCollection::class, $specification->getOperations());
        $this->assertEquals(['/foo'], array_keys($specification->getOperations()->getArrayCopy()));
    }
    */
}
