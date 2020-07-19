<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2019 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Tests\Resource;

use PHPUnit\Framework\TestCase;
use PSX\Api\Resource\Factory;
use PSX\Schema\Property;
use PSX\Schema\Schema;

/**
 * MethodAbstractTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class MethodAbstractTest extends TestCase
{
    public function testMethod()
    {
        $method = Factory::getMethod('POST');
        $method->setDescription('foobar');
        $method->setQueryParameters('GetQuery');
        $method->setRequest('Request');
        $method->addResponse(200, 'Response');
        $method->setTags(['Foo']);

        $this->assertEquals('foobar', $method->getDescription());
        $this->assertEquals('GetQuery', $method->getQueryParameters());
        $this->assertTrue($method->hasRequest());
        $this->assertEquals('Request', $method->getRequest());
        $this->assertEquals('Response', $method->getResponse(200));
        $this->assertTrue($method->hasResponse(200));
        $this->assertFalse($method->hasResponse(201));
        $this->assertTrue($method->hasQueryParameters());
        $this->assertInternalType('array', $method->getTags());
        $this->assertEquals('Foo', current($method->getTags()));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetResponseInvalid()
    {
        Factory::getMethod('POST')->getResponse(500);
    }
}
