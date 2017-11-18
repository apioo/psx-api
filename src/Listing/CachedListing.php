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

use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\ListingInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Schema\Schema;

/**
 * CachedListing
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CachedListing implements ListingInterface
{
    /**
     * @var \PSX\Api\ListingInterface
     */
    protected $listing;

    /**
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var integer|null
     */
    protected $expire;

    /**
     * @param \PSX\Api\ListingInterface $listing
     * @param \Psr\Cache\CacheItemPoolInterface $cache
     * @param integer|null $expire
     */
    public function __construct(ListingInterface $listing, CacheItemPoolInterface $cache, $expire = null)
    {
        $this->listing = $listing;
        $this->cache   = $cache;
        $this->expire  = $expire;
    }

    /**
     * @inheritdoc
     */
    public function getResourceIndex(FilterInterface $filter = null)
    {
        $item = $this->cache->getItem($this->getResourceIndexKey($filter));

        if ($item->isHit()) {
            return $item->get();
        } else {
            $result = $this->listing->getResourceIndex($filter);

            $item->set($result);
            $item->expiresAfter($this->expire);

            $this->cache->save($item);

            return $result;
        }
    }

    /**
     * @inheritdoc
     */
    public function getResource($sourcePath, $version = null)
    {
        $item = $this->cache->getItem($this->getResourceKey($sourcePath, $version));

        if ($item->isHit()) {
            return $item->get();
        } else {
            $resource = $this->listing->getResource($sourcePath, $version);

            if ($resource instanceof Resource) {
                $this->materializeResource($resource);

                $item->set($resource);
                $item->expiresAfter($this->expire);

                $this->cache->save($item);

                return $resource;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getResourceCollection($version = null, FilterInterface $filter = null)
    {
        $item = $this->cache->getItem($this->getResourceCollectionKey($version, $filter));

        if ($item->isHit()) {
            return $item->get();
        } else {
            $collection = $this->listing->getResourceCollection($version, $filter);

            if ($collection instanceof ResourceCollection) {
                $this->materializeCollection($collection);

                $item->set($collection);
                $item->expiresAfter($this->expire);

                $this->cache->save($item);

                return $collection;
            }
        }

        return new ResourceCollection();
    }

    /**
     * Invalidates the cached resource index
     */
    public function invalidateResourceIndex(FilterInterface $filter = null)
    {
        $this->cache->deleteItem($this->getResourceIndexKey($filter));
    }

    /**
     * Invalidates a cached resource
     * 
     * @param string $sourcePath
     * @param integer|null $version
     */
    public function invalidateResource($sourcePath, $version = null)
    {
        $this->cache->deleteItem($this->getResourceKey($sourcePath, $version));
    }

    /**
     * Invalidates the cached resource collection
     * 
     * @param integer|null $version
     */
    public function invalidateResourceCollection($version = null, FilterInterface $filter = null)
    {
        $this->cache->deleteItem($this->getResourceCollectionKey($version, $filter));
    }

    /**
     * A collection can contain resources which are only resolved if we actual 
     * call the getDefinition method i.e. the schema is stored in a database.
     * This resolves the resources inside a collection
     *
     * @param \PSX\Api\ResourceCollection $collection
     */
    protected function materializeCollection(ResourceCollection $collection)
    {
        foreach ($collection as $resource) {
            $this->materializeResource($resource);
        }
    }

    /**
     * A resource can contain schema definitions which are only resolved if we
     * actual call the getDefinition method i.e. the schema is stored in a
     * database. So before we cache the documentation we must get the actual
     * definition object which we can serialize
     *
     * @param \PSX\Api\Resource $resource
     */
    protected function materializeResource(Resource $resource)
    {
        foreach ($resource as $method) {
            $request = $method->getRequest();
            if ($request) {
                $method->setRequest(new Schema($request->getDefinition()));
            }

            $responses = $method->getResponses();
            foreach ($responses as $statusCode => $response) {
                $method->addResponse($statusCode, new Schema($response->getDefinition()));
            }
        }
    }

    /**
     * @return string
     */
    protected function getResourceIndexKey(FilterInterface $filter = null)
    {
        return 'api-resource-index' . ($filter !== null ? '-' . $filter->getId() : '');
    }

    /**
     * @param string $path
     * @param integer|null $version
     * @return string
     */
    protected function getResourceKey($path, $version = null)
    {
        return 'api-resource-' . substr(md5($path), 0, 16) . '-' . intval($version);
    }

    /**
     * @param integer|null $version
     * @return string
     */
    protected function getResourceCollectionKey($version = null, FilterInterface $filter = null)
    {
        return 'api-resource-collection-' . intval($version) . ($filter !== null ? '-' . $filter->getId() : '');
    }
}
