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

namespace PSX\Api;

use PSX\Api\Listing\FilterInterface;

/**
 * ResourceCollection
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 * @extends \Traversable<string, Resource>
 */
class ResourceCollection extends \ArrayObject
{
    public function __construct(array $input = [])
    {
        parent::__construct([]);

        foreach ($input as $resource) {
            $this->set($resource);
        }
    }

    public function set(Resource $resource): void
    {
        $this->offsetSet($resource->getPath(), $resource);
    }

    public function has(string $path): bool
    {
        return $this->offsetExists($path);
    }

    public function get(string $path): ?Resource
    {
        return $this->offsetExists($path) ? $this->offsetGet($path) : null;
    }

    /**
     * Returns the first resource of this collection
     */
    public function getFirst(): ?Resource
    {
        $iterator = $this->getIterator();
        $iterator->rewind();
        return $iterator->current() ?: null;
    }

    public function filter(FilterInterface $filter): ResourceCollection
    {
        $collection = new ResourceCollection();
        foreach ($this->getIterator() as $resource) {
            if ($filter->match($resource->getPath())) {
                $collection->set($resource);
            }
        }

        return $collection;
    }
}
