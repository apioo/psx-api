<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\Resource;
use PSX\Api\SpecificationInterface;
use PSX\Api\Tests\Parser\Annotation\TestController;

/**
 * ApiManagerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiManagerTest extends ApiManagerTestCase
{
    public function testGetApiAnnotation()
    {
        $specification = $this->apiManager->getApi(TestController::class, '/foo');

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
    }

    public function testGetApiOpenAPI()
    {
        $specification = $this->apiManager->getApi(__DIR__ . '/Parser/openapi/test.json', '/foo/:fooId');

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
    }

    public function testGetApiFileDoesNotExist()
    {
        $this->expectException(\RuntimeException::class);

        $this->apiManager->getApi(__DIR__ . '/Parser/openapi/unknown.json', '/foo/:fooId');
    }

    public function testGetApiInvalidType()
    {
        $this->expectException(\RuntimeException::class);

        $this->apiManager->getApi('', '/foo', 12);
    }
    
    public function testGetBuilder()
    {
        $builder = $this->apiManager->getBuilder(Resource::STATUS_ACTIVE, '/foo');
        $builder->setTitle('My_Resource');
        $builder->setDescription('My super resource');
        $builder->setPathParameters('Path')->addInteger('todo_id');
        $builder->setTags(['my_tag']);
        
        $post = $builder->addMethod('POST');
        $post->setOperationId('my_action');
        $post->setQueryParameters('PostQuery')->addInteger('startIndex');
        $post->setDescription('My method description');
        $post->setRequest(__DIR__ . '/Parser/schema/schema.json');
        $post->addResponse(200, __DIR__ . '/Parser/schema/schema.json');
        $post->setSecurity('OAuth2', ['foo']);
        $post->setTags(['my_tag']);

        $specification = $builder->getSpecification();

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
    }
}
