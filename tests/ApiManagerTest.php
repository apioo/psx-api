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

namespace PSX\Api\Tests;

use PSX\Api\Exception\ParserException;
use PSX\Api\SpecificationInterface;
use PSX\Api\Tests\Parser\Attribute\TestController;
use PSX\Schema\SchemaManager;

/**
 * ApiManagerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ApiManagerTest extends ApiManagerTestCase
{
    public function testGetApiAttribute()
    {
        $specification = $this->apiManager->getApi(TestController::class);

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
    }

    public function testGetApiTypeAPI()
    {
        $specification = $this->apiManager->getApi(__DIR__ . '/Parser/typeapi/simple.json');

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
    }

    public function testGetApiOpenAPI()
    {
        $specification = $this->apiManager->getApi(__DIR__ . '/Parser/openapi/simple.json');

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
    }

    public function testGetApiFileDoesNotExist()
    {
        $this->expectException(ParserException::class);

        $this->apiManager->getApi(__DIR__ . '/Parser/openapi/unknown.json');
    }

    public function testGetApiInvalidType()
    {
        $this->expectException(ParserException::class);

        $this->apiManager->getApi('', 12);
    }
    
    public function testGetBuilder()
    {
        $builder = $this->apiManager->getBuilder();
        $manager = new SchemaManager();

        $schema = $manager->getSchema(__DIR__ . '/Parser/schema/schema.json');
        $builder->addDefinitions($schema->getDefinitions());

        $operation = $builder->addOperation('my.operation', 'GET', '/foo', 200, $schema->getType());
        $operation->addArgument('payload', 'body', $schema->getType());
        $operation->setDescription('My operation description');
        $operation->setSecurity(['foo']);
        $operation->setTags(['my_tag']);

        $specification = $builder->getSpecification();

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
    }
}
