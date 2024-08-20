<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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
 * @link    https://phpsx.org
 */
class InflectionTest extends TestCase
{
    /**
     * @dataProvider convertPlaceholderToCurlyProvider
     */
    public function testConvertPlaceholderToCurly(string $expect, string $route)
    {
        $this->assertEquals($expect, Inflection::convertPlaceholderToCurly($route));
    }

    public function convertPlaceholderToCurlyProvider(): array
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
     * @dataProvider convertPlaceholderToColonProvider
     */
    public function testConvertPlaceholderToColon(string $expect, string $route)
    {
        $this->assertEquals($expect, Inflection::convertPlaceholderToColon($route));
    }

    public function convertPlaceholderToColonProvider(): array
    {
        return [
            ['/foo', '/foo'],
            ['/foo/:bar', '/foo/{bar}'],
            ['/foo/:bar/foo', '/foo/{bar}/foo'],
            ['/foo/:bar/foo/:baz', '/foo/{bar}/foo/{baz}'],
        ];
    }

    /**
     * @dataProvider extractPlaceholderNamesProvider
     */
    public function testExtractPlaceholderNames(string $path, array $names)
    {
        $this->assertEquals($names, Inflection::extractPlaceholderNames($path));
    }

    public function extractPlaceholderNamesProvider(): array
    {
        return [
            ['/foo', []],
            ['/foo/:bar', ['bar']],
            ['/foo/*bar', ['bar']],
            ['/foo/$bar<[0-9]+>', ['bar']],
            ['/foo/{bar}', ['bar']],
            ['/foo/:bar/foo', ['bar']],
            ['/foo/*bar/foo', ['bar']],
            ['/foo/$bar<[0-9]+>/foo', ['bar']],
            ['/foo/{bar}/foo', ['bar']],
            ['/foo/:bar/foo/:baz', ['bar', 'baz']],
            ['/foo/*bar/foo/*baz', ['bar', 'baz']],
            ['/foo/$bar<[0-9]+>/foo/$baz<[0-9]+>', ['bar', 'baz']],
            ['/foo/{bar}/foo/{baz}', ['bar', 'baz']],
            ['/foo/:bar/foo/:baz/foo', ['bar', 'baz']],
            ['/foo/*bar/foo/*baz/foo', ['bar', 'baz']],
            ['/foo/$bar<[0-9]+>/foo/$baz<[0-9]+>/foo', ['bar', 'baz']],
            ['/foo/{bar}/foo/{baz}/foo', ['bar', 'baz']],
        ];
    }
}
