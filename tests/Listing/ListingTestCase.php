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

namespace PSX\Api\Tests\Listing;

use PHPUnit\Framework\TestCase;
use PSX\Api\Listing\Filter\RegxpFilter;
use PSX\Api\Listing\Route;
use PSX\Api\Resource;
use PSX\Api\SpecificationInterface;

/**
 * ListingTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class ListingTestCase extends TestCase
{
    public function testGetAvailableRoutes()
    {
        $listing = $this->newListing();
        $routes  = $listing->getAvailableRoutes();

        $this->assertIsArray($routes);
        $this->assertEquals(2, count($routes));
        $this->assertInstanceOf(Route::class, $routes[0]);
        $this->assertEquals('/foo', $routes[0]->getPath());
        $this->assertEquals('/bar', $routes[1]->getPath());

        $routes = $listing->getAvailableRoutes();

        $this->assertIsArray($routes);
        $this->assertEquals(2, count($routes));
        $this->assertInstanceOf(Route::class, $routes[0]);
        $this->assertEquals('/foo', $routes[0]->getPath());
        $this->assertEquals('/bar', $routes[1]->getPath());
    }

    public function testGetAvailableRoutesFilter()
    {
        $listing = $this->newListing();
        $routes  = $listing->getAvailableRoutes(new RegxpFilter('^/foo'));

        $this->assertIsArray($routes);
        $this->assertEquals(1, count($routes));
        $this->assertInstanceOf(Route::class, $routes[0]);
        $this->assertEquals('/foo', $routes[0]->getPath());

        $routes = $listing->getAvailableRoutes(new RegxpFilter('^/bar'));

        $this->assertIsArray($routes);
        $this->assertEquals(1, count($routes));
        $this->assertInstanceOf(Route::class, $routes[0]);
        $this->assertEquals('/bar', $routes[0]->getPath());
    }

    public function testFind()
    {
        $listing = $this->newListing();
        $specification = $listing->find('/foo');

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
        $this->assertEquals('/foo', $specification->getResourceCollection()->get('/foo')->getPath());

        $specification = $listing->find('/foo');

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
        $this->assertEquals('/foo', $specification->getResourceCollection()->get('/foo')->getPath());

        $specification = $listing->find('/bar');

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
        $this->assertEquals('/bar', $specification->getResourceCollection()->get('/bar')->getPath());
    }

    public function testFindAll()
    {
        $listing = $this->newListing();
        $specification = $listing->findAll();

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
        $this->assertEquals(2, $specification->getResourceCollection()->count());
        $this->assertInstanceOf(Resource::class, $specification->getResourceCollection()->get('/foo'));
        $this->assertInstanceOf(Resource::class, $specification->getResourceCollection()->get('/bar'));

        $specification = $listing->findAll();

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
        $this->assertEquals(2, $specification->getResourceCollection()->count());
        $this->assertInstanceOf(Resource::class, $specification->getResourceCollection()->get('/foo'));
        $this->assertInstanceOf(Resource::class, $specification->getResourceCollection()->get('/bar'));
    }

    public function testFindAllFilter()
    {
        $listing = $this->newListing();
        $specification = $listing->findAll(null, new RegxpFilter('^/foo'));

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
        $this->assertEquals(1, $specification->getResourceCollection()->count());
        $this->assertInstanceOf(Resource::class, $specification->getResourceCollection()->get('/foo'));

        $specification = $listing->findAll(null, new RegxpFilter('^/bar'));

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
        $this->assertEquals(1, $specification->getResourceCollection()->count());
        $this->assertInstanceOf(Resource::class, $specification->getResourceCollection()->get('/bar'));
    }

    /**
     * @return \PSX\Api\ListingInterface
     */
    abstract protected function newListing();
}
