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

namespace PSX\Api\Tests\Listing;

use PSX\Api\ApiManager;
use PSX\Api\Listing\MemoryListing;
use PSX\Api\Listing\Route;
use PSX\Api\Tests\Parser\Attribute\FooController;
use PSX\Api\Tests\Parser\Attribute\TestController;
use PSX\Schema\SchemaManager;

/**
 * MemoryListingTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class MemoryListingTest extends ListingTestCase
{
    protected function newListing()
    {
        $apiManager = new ApiManager(new SchemaManager());

        $listing = new MemoryListing();
        $listing->addRoute(new Route('/foo', ['GET'], '*'));
        $listing->addRoute(new Route('/bar', ['GET'], '*'));
        $listing->addSpecification($apiManager->getApi(TestController::class, '/foo'));
        $listing->addSpecification($apiManager->getApi(FooController::class, '/bar'));

        return $listing;
    }
}
