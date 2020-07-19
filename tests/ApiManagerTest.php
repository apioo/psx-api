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

    /**
     * @expectedException \RuntimeException
     */
    public function testGetApiFileDoesNotExist()
    {
        $this->apiManager->getApi(__DIR__ . '/Parser/openapi/unknown.json', '/foo/:fooId');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetApiInvalidType()
    {
        $this->apiManager->getApi('', '/foo', 12);
    }
}
