<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\SpecificationInterface;

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
    public function getAvailableRoutes(FilterInterface $filter = null): iterable
    {
        $item = $this->cache->getItem($this->getResourceIndexKey($filter));

        if ($item->isHit()) {
            return $item->get();
        }

        $result = $this->listing->getAvailableRoutes($filter);

        $item->set($result);
        $item->expiresAfter($this->expire);

        $this->cache->save($item);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function find(string $path, ?int $version = null): ?SpecificationInterface
    {
        $item = $this->cache->getItem($this->getResourceKey($path, $version));

        if ($item->isHit()) {
            return $item->get();
        }

        $specification = $this->listing->find($path, $version);
        if (!$specification instanceof SpecificationInterface) {
            return null;
        }

        $item->set($specification);
        $item->expiresAfter($this->expire);

        $this->cache->save($item);

        return $specification;
    }

    /**
     * @inheritdoc
     */
    public function findAll(?int $version = null, FilterInterface $filter = null): SpecificationInterface
    {
        $item = $this->cache->getItem($this->getResourceCollectionKey($version, $filter));

        if ($item->isHit()) {
            return $item->get();
        }

        $collection = $this->listing->findAll($version, $filter);

        $item->set($collection);
        $item->expiresAfter($this->expire);

        $this->cache->save($item);

        return $collection;
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
