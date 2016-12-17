<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * ParserTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ParserTestCase extends \PHPUnit_Framework_TestCase
{
    public function testParseSimple()
    {
        $resource = $this->getResource();

        $this->assertEquals('/foo', $resource->getPath());
        $this->assertEquals('Test', $resource->getTitle());
        $this->assertEquals('Test description', $resource->getDescription());

        $path = $resource->getPathParameters();

        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $path);
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $path->getProperty('fooId'));

        $methods = $resource->getMethods();

        $this->assertEquals(['GET'], array_keys($methods));

        $this->assertEquals('Test description', $methods['GET']->getDescription());

        $query = $methods['GET']->getQueryParameters();

        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $query->getProperty('foo'));
        $this->assertEquals('Test', $query->getProperty('foo')->getDescription());
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $query->getProperty('bar'));
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $query->getProperty('baz'));
        $this->assertEquals(['foo', 'bar'], $query->getProperty('baz')->getEnum());
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $query->getProperty('boz'));
        $this->assertEquals('[A-z]+', $query->getProperty('boz')->getPattern());
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $query);
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $query->getProperty('integer'));
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $query->getProperty('number'));
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $query->getProperty('date'));
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $query->getProperty('boolean'));
        $this->assertInstanceOf('PSX\Schema\PropertyInterface', $query->getProperty('string'));

        $request = $methods['GET']->getRequest();

        $this->assertInstanceOf('PSX\Schema\SchemaInterface', $request);

        $response = $methods['GET']->getResponse(200);

        $this->assertInstanceOf('PSX\Schema\SchemaInterface', $response);
    }

    abstract protected function getResource();
}
