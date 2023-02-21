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

use PSX\Api\ListingInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Definitions;

/**
 * MemoryListing
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class MemoryListing implements ListingInterface
{
    private array $routes;
    private SpecificationInterface $specification;

    public function __construct()
    {
        $this->routes = [];
        $this->specification = new Specification(
            new ResourceCollection(),
            new Definitions()
        );
    }

    public function getAvailableRoutes(?FilterInterface $filter = null): iterable
    {
        if ($filter !== null) {
            return array_values(array_filter($this->routes, static function(Route $route) use ($filter){
                return $filter->match($route->getPath());
            }));
        } else {
            return $this->routes;
        }
    }

    public function find(string $path, ?string $version = null): ?SpecificationInterface
    {
        $resource = $this->specification->getResourceCollection()->get($path);
        if (!$resource instanceof Resource) {
            return null;
        }

        $collection = new ResourceCollection();
        $collection->set($resource);

        return new Specification(
            $collection,
            $this->specification->getDefinitions()
        );
    }

    public function findAll(?string $version = null, ?FilterInterface $filter = null): SpecificationInterface
    {
        if ($filter !== null) {
            return new Specification(
                $this->specification->getResourceCollection()->filter($filter),
                $this->specification->getDefinitions()
            );
        } else {
            return $this->specification;
        }
    }

    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function addSpecification(SpecificationInterface $specification): void
    {
        $this->specification->merge($specification);
    }
}
