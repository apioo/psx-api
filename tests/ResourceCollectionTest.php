<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PHPUnit\Framework\TestCase;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;

/**
 * ResourceCollectionTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceCollectionTest extends TestCase
{
    public function testCollection()
    {
        $collection = new ResourceCollection();
        $resource   = new Resource(Resource::STATUS_ACTIVE, '/foo');

        $this->assertNull($collection->get('/foo'));
        $this->assertFalse($collection->has('/foo'));
        
        $collection->set($resource);

        $this->assertInstanceOf(Resource::class, $collection->get('/foo'));
        $this->assertTrue($collection->has('/foo'));
    }
}
