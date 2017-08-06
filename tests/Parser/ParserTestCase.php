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

use PSX\Api\Tests\ApiManagerTestCase;
use PSX\Schema\PropertyInterface;
use PSX\Schema\SchemaInterface;

/**
 * ParserTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ParserTestCase extends ApiManagerTestCase
{
    public function testParseSimple()
    {
        $resource = $this->getResource();

        $this->assertEquals('/foo', $resource->getPath());
        $this->assertEquals('Test', $resource->getTitle());
        $this->assertEquals('Test description', $resource->getDescription());

        $path = $resource->getPathParameters();

        $this->assertInstanceOf(PropertyInterface::class, $path);
        $this->assertInstanceOf(PropertyInterface::class, $path->getProperty('fooId'));

        $methods = $resource->getMethods();

        $this->assertEquals(['GET'], array_keys($methods));

        $this->assertEquals('A long **Test** description', $methods['GET']->getDescription());

        $query = $methods['GET']->getQueryParameters();

        $this->assertInstanceOf(PropertyInterface::class, $query->getProperty('foo'));
        $this->assertEquals('Test', $query->getProperty('foo')->getDescription());
        $this->assertInstanceOf(PropertyInterface::class, $query->getProperty('bar'));
        $this->assertInstanceOf(PropertyInterface::class, $query->getProperty('baz'));
        $this->assertEquals(['foo', 'bar'], $query->getProperty('baz')->getEnum());
        $this->assertInstanceOf(PropertyInterface::class, $query->getProperty('boz'));
        $this->assertEquals('[A-z]+', $query->getProperty('boz')->getPattern());
        $this->assertInstanceOf(PropertyInterface::class, $query);
        $this->assertInstanceOf(PropertyInterface::class, $query->getProperty('integer'));
        $this->assertInstanceOf(PropertyInterface::class, $query->getProperty('number'));
        $this->assertInstanceOf(PropertyInterface::class, $query->getProperty('date'));
        $this->assertInstanceOf(PropertyInterface::class, $query->getProperty('boolean'));
        $this->assertInstanceOf(PropertyInterface::class, $query->getProperty('string'));

        $request = $methods['GET']->getRequest();

        $this->assertInstanceOf(SchemaInterface::class, $request);
        $this->assertInstanceOf(PropertyInterface::class, $request->getDefinition()->getProperty('artist'));

        $response = $methods['GET']->getResponse(200);

        $this->assertInstanceOf(SchemaInterface::class, $response);
        $this->assertInstanceOf(PropertyInterface::class, $response->getDefinition()->getProperty('artist'));
    }

    /**
     * @return \PSX\Api\Resource
     */
    abstract protected function getResource();
}
