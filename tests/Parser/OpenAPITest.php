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

namespace PSX\Api\Tests\Parser;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Api\ApiManager;
use PSX\Api\Parser\OpenAPI;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Type\StructType;
use PSX\Schema\TypeInterface;

/**
 * OpenAPITest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OpenAPITest extends ParserTestCase
{
    /**
     * @inheritDoc
     */
    protected function getSpecification(): SpecificationInterface
    {
        return $this->apiManager->getApi(__DIR__ . '/openapi/simple.json', '/foo', ApiManager::TYPE_OPENAPI);
    }

    public function testParseComplex()
    {
        $specification = OpenAPI::fromFile(__DIR__ . '/openapi/complex.json', '/foo');

        $resource = $specification->getResourceCollection()->get('/foo');
        $definitions = $specification->getDefinitions();

        $this->assertEquals('/foo', $resource->getPath());
        $this->assertEquals('Test', $resource->getTitle());
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

        $this->assertInstanceOf(Resource::class, $specification->getResourceCollection()->get('/foo/:fooId'));
    }

    public function testParseInvalidPath()
    {
        $specification = OpenAPI::fromFile(__DIR__ . '/openapi/test.json', '/test');

        $this->assertEquals(0, count($specification->getResourceCollection()));
    }

    public function testParseAll()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Schema\\Annotation');

        $parser = new OpenAPI($reader, __DIR__ . '/openapi');
        $specification = $parser->parse(file_get_contents(__DIR__ . '/openapi/simple.json'));

        $this->assertInstanceOf(ResourceCollection::class, $specification->getResourceCollection());
        $this->assertEquals(['/foo'], array_keys($specification->getResourceCollection()->getArrayCopy()));
    }
}
