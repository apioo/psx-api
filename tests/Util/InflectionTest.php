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

namespace PSX\Api\Tests\Resource\Util;

use PHPUnit\Framework\TestCase;
use PSX\Api\Util\Inflection;

/**
 * InflectionTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class InflectionTest extends TestCase
{
    /**
     * @param string $expect
     * @param string $route
     * @dataProvider convertPlaceholderToCurlyProvider
     */
    public function testConvertPlaceholderToCurly($expect, $route)
    {
        $this->assertEquals($expect, Inflection::convertPlaceholderToCurly($route));
    }

    public function convertPlaceholderToCurlyProvider()
    {
        return [
            ['/foo', '/foo'],
            ['/foo/{bar}', '/foo/:bar'],
            ['/foo/{bar}', '/foo/*bar'],
            ['/foo/{bar}', '/foo/$bar<[0-9]+>'],
            ['/foo/{bar}/foo', '/foo/:bar/foo'],
            ['/foo/{bar}/foo', '/foo/*bar/foo'],
            ['/foo/{bar}/foo', '/foo/$bar<[0-9]+>/foo'],
            ['/foo/{bar}/foo/{baz}', '/foo/:bar/foo/:baz'],
            ['/foo/{bar}/foo/{baz}', '/foo/*bar/foo/*baz'],
            ['/foo/{bar}/foo/{baz}', '/foo/$bar<[0-9]+>/foo/$baz<[0-9]+>'],
            ['/foo/{bar}/foo/{baz}/foo', '/foo/:bar/foo/:baz/foo'],
            ['/foo/{bar}/foo/{baz}/foo', '/foo/*bar/foo/*baz/foo'],
            ['/foo/{bar}/foo/{baz}/foo', '/foo/$bar<[0-9]+>/foo/$baz<[0-9]+>/foo'],
        ];
    }

    /**
     * @param string $expect
     * @param string $route
     * @dataProvider convertPlaceholderToColonProvider
     */
    public function testConvertPlaceholderToColon($expect, $route)
    {
        $this->assertEquals($expect, Inflection::convertPlaceholderToColon($route));
    }

    public function convertPlaceholderToColonProvider()
    {
        return [
            ['/foo', '/foo'],
            ['/foo/:bar', '/foo/{bar}'],
            ['/foo/:bar/foo', '/foo/{bar}/foo'],
            ['/foo/:bar/foo/:baz', '/foo/{bar}/foo/{baz}'],
        ];
    }

    /**
     * @param string $expect
     * @param string $route
     * @dataProvider generateTitleFromRouteProvider
     */
    public function testGenerateTitleFromRoute($expect, $route)
    {
        $this->assertEquals($expect, Inflection::generateTitleFromRoute($route));
    }

    public function generateTitleFromRouteProvider()
    {
        return [
            ['Foo', '/foo'],
            ['FooBar', '/foo/:bar'],
            ['FooBar', '/foo/*bar'],
            ['FooBar', '/foo/$bar<[0-9]+>'],
            ['FooBarFoo', '/foo/:bar/foo'],
            ['FooBarFoo', '/foo/*bar/foo'],
            ['FooBarFoo', '/foo/$bar<[0-9]+>/foo'],
            ['FooBarFooBaz', '/foo/:bar/foo/:baz'],
            ['FooBarFooBaz', '/foo/*bar/foo/*baz'],
            ['FooBarFooBaz', '/foo/$bar<[0-9]+>/foo/$baz<[0-9]+>'],
            ['FooBarFooBazFoo', '/foo/:bar/foo/:baz/foo'],
            ['FooBarFooBazFoo', '/foo/*bar/foo/*baz/foo'],
            ['FooBarFooBazFoo', '/foo/$bar<[0-9]+>/foo/$baz<[0-9]+>/foo'],
        ];
    }
}
