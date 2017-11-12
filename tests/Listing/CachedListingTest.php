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

use Doctrine\Common\Cache\ArrayCache;
use PSX\Api\ApiManager;
use PSX\Api\Listing\CachedListing;
use PSX\Api\Listing\MemoryListing;
use PSX\Api\Tests\Parser\Annotation\FooController;
use PSX\Api\Tests\Parser\Annotation\TestController;
use PSX\Cache\Pool;
use PSX\Schema\SchemaManager;

/**
 * CachedListingTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CachedListingTest extends ListingTestCase
{
    protected function newListing()
    {
        $schemaReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $schemaReader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $apiReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $apiReader->addNamespace('PSX\\Api\\Annotation');

        $apiManager = new ApiManager($apiReader, new SchemaManager($schemaReader));

        $listing = new MemoryListing();
        $listing->addResource($apiManager->getApi(TestController::class, '/foo'));
        $listing->addResource($apiManager->getApi(FooController::class, '/bar'));

        $cache = new Pool(new ArrayCache());

        return new CachedListing($listing, $cache);
    }

    public function testInvalidateResourceIndex()
    {
        $listing = $this->newListing();
        $listing->invalidateResourceIndex();
    }

    public function testInvalidateResource()
    {
        $listing = $this->newListing();
        $listing->invalidateResource('/foo');
    }

    public function testInvalidateResourceCollection()
    {
        $listing = $this->newListing();
        $listing->invalidateResourceCollection();
    }
}
