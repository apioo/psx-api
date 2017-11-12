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

namespace PSX\Api\Tests\Listing;

use PSX\Api\Listing\Filter\RegxpFilter;
use PSX\Api\Listing\FilterFactory;
use PSX\Api\Listing\FilterInterface;

/**
 * FilterFactoryTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FilterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $factory = new FilterFactory();
        $factory->addFilter('foo', new RegxpFilter('^/foo'));
        $factory->addFilter('bar', new RegxpFilter('^/bar'));

        $this->assertInstanceOf(FilterInterface::class, $factory->getFilter('foo'));
        $this->assertEquals('c9d9bfae', $factory->getFilter('foo')->getId());
        $this->assertInstanceOf(FilterInterface::class, $factory->getFilter('bar'));
        $this->assertEquals('a1841e70', $factory->getFilter('bar')->getId());
        $this->assertNull($factory->getFilter('baz'));
    }

    public function testFactoryDefault()
    {
        $factory = new FilterFactory();
        $factory->addFilter('foo', new RegxpFilter('^/foo'));
        $factory->addFilter('bar', new RegxpFilter('^/bar'));
        $factory->setDefault('bar');

        $this->assertInstanceOf(FilterInterface::class, $factory->getFilter('foo'));
        $this->assertEquals('c9d9bfae', $factory->getFilter('foo')->getId());
        $this->assertInstanceOf(FilterInterface::class, $factory->getFilter('bar'));
        $this->assertEquals('a1841e70', $factory->getFilter('bar')->getId());
        $this->assertInstanceOf(FilterInterface::class, $factory->getFilter('baz'));
        $this->assertEquals('a1841e70', $factory->getFilter('baz')->getId());
    }

    public function testFactoryEmpty()
    {
        $factory = new FilterFactory();

        $this->assertNull($factory->getFilter(null));
        $this->assertNull($factory->getFilter(''));
        $this->assertNull($factory->getFilter('foo'));
    }
}
