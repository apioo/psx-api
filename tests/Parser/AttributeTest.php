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

use PSX\Api\ApiManager;
use PSX\Api\Parser\Attribute as AttributeParser;
use PSX\Api\SpecificationInterface;
use PSX\Api\Tests\Parser\Attribute\TestController;

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
        return $this->apiManager->getApi(TestController::class, '/foo', ApiManager::TYPE_ATTRIBUTE);
    }

    public function testOperationId()
    {
        $specification = $this->apiManager->getApi(TestController::class, '/foo');
        $resource = $specification->getResourceCollection()->get('/foo');

        $this->assertEquals('PSX_Api_Tests_Parser_Attribute_TestController_doGet', $resource->getMethod('GET')->getOperationId());
    }

    public function testParseInvalid()
    {
        $this->expectException(\ReflectionException::class);

        $annotation = new AttributeParser($this->schemaManager);
        $annotation->parse('foo', '/foo');
    }
}
