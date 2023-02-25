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

namespace PSX\Api\Listing;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use PSX\Api\ListingInterface;
use PSX\Api\SpecificationInterface;

/**
 * CachedListing
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class CachedListing implements ListingInterface
{
    private ListingInterface $listing;
    private CacheItemPoolInterface $cache;
    private ?int $expire;

    public function __construct(ListingInterface $listing, CacheItemPoolInterface $cache, ?int $expire = null)
    {
        $this->listing = $listing;
        $this->cache   = $cache;
        $this->expire  = $expire;
    }

    public function getNames(?FilterInterface $filter = null): array
    {
        $item = $this->cache->getItem($this->getResourceIndexKey($filter));
        if ($item->isHit()) {
            return $item->get();
        }

        $result = $this->listing->getNames($filter);

        $item->set($result);
        $item->expiresAfter($this->expire);

        $this->cache->save($item);

        return $result;
    }

    public function find(string $path, ?string $version = null): ?SpecificationInterface
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

    public function findAll(?string $version = null, ?FilterInterface $filter = null): SpecificationInterface
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
     *
     * @throws InvalidArgumentException
     */
    public function invalidateResourceIndex(?FilterInterface $filter = null): void
    {
        $this->cache->deleteItem($this->getResourceIndexKey($filter));
    }

    /**
     * Invalidates a cached resource
     *
     * @throws InvalidArgumentException
     */
    public function invalidateResource(string $sourcePath, ?string $version = null): void
    {
        $this->cache->deleteItem($this->getResourceKey($sourcePath, $version));
    }

    /**
     * Invalidates the cached resource collection
     *
     * @throws InvalidArgumentException
     */
    public function invalidateResourceCollection(?string $version = null, ?FilterInterface $filter = null): void
    {
        $this->cache->deleteItem($this->getResourceCollectionKey($version, $filter));
    }

    private function getResourceIndexKey(?FilterInterface $filter = null): string
    {
        return 'api-resource-index' . ($filter !== null ? '-' . $filter->getId() : '');
    }

    private function getResourceKey(string $path, ?string $version = null): string
    {
        return 'api-resource-' . substr(md5($path), 0, 16) . '-' . intval($version);
    }

    private function getResourceCollectionKey(?string $version = null, ?FilterInterface $filter = null): string
    {
        return 'api-resource-collection-' . intval($version) . ($filter !== null ? '-' . $filter->getId() : '');
    }
}
