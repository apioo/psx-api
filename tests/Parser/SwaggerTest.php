<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\ApiManager;
use PSX\Api\Parser\Swagger;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;

/**
 * SwaggerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SwaggerTest extends ParserTestCase
{
    protected function getResource()
    {
        return $this->apiManager->getApi(__DIR__ . '/swagger/simple.json', '/foo', ApiManager::TYPE_SWAGGER);
    }

    public function testParsePath()
    {
        $resource = Swagger::fromFile(__DIR__ . '/openapi/test.json', '/foo/:fooId');

        $this->assertInstanceOf(Resource::class, $resource);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseInvalidPath()
    {
        Swagger::fromFile(__DIR__ . '/swagger/test.json', '/test');
    }

    public function testParseDb()
    {
        $parser = new Swagger(__DIR__ . '/swagger');
        $result = $parser->parseAll(file_get_contents(__DIR__ . '/swagger/db.json'));

        $this->assertInstanceOf(ResourceCollection::class, $result);
        $this->assertEquals(['/location.name', '/departureBoard', '/arrivalBoard', '/journeyDetail'], array_keys($result->getArrayCopy()));
    }

    public function testParseAll()
    {
        $parser = new Swagger(__DIR__ . '/swagger');
        $result = $parser->parseAll(file_get_contents(__DIR__ . '/swagger/simple.json'));

        $this->assertInstanceOf(ResourceCollection::class, $result);
        $this->assertEquals(['/foo'], array_keys($result->getArrayCopy()));
    }
}
