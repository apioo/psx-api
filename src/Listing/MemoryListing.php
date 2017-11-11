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

namespace PSX\Api\Listing;

use PSX\Api\ListingInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;

/**
 * MemoryListing
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class MemoryListing implements ListingInterface
{
    /**
     * @var \PSX\Api\Resource[]
     */
    protected $resources;

    public function __construct()
    {
        $this->resources = [];
    }

    /**
     * @return \PSX\Api\Resource[]
     */
    public function getResourceIndex()
    {
        return $this->resources;
    }

    /**
     * @param string $sourcePath
     * @param integer|null $version
     * @return \PSX\Api\Resource
     */
    public function getResource($sourcePath, $version = null)
    {
        foreach ($this->resources as $resource) {
            if ($resource->getPath() == $sourcePath) {
                return $resource;
            }
        }

        return null;
    }

    /**
     * @param integer|null $version
     * @param \PSX\Api\Listing\FilterInterface|null $filter
     * @return \PSX\Api\ResourceCollection
     */
    public function getResourceCollection($version = null, FilterInterface $filter = null)
    {
        $collection = new ResourceCollection();

        foreach ($this->resources as $resource) {
            if ($filter !== null) {
                if (!$filter->match($resource->getPath())) {
                    continue;
                }
            }

            $collection->set($resource);
        }

        return $collection;
    }

    /**
     * @param \PSX\Api\Resource $resource
     */
    public function addResource(Resource $resource)
    {
        $this->resources[] = $resource;
    }
}
