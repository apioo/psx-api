<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2019 Christoph Kappestein <christoph.kappestein@gmail.com>
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
 * @link    http://phpsx.org
 */
class ResourceCollection extends \ArrayObject
{
    public function __construct(iterable $input = [])
    {
        parent::__construct([]);

        foreach ($input as $resource) {
            $this->set($resource);
        }
    }

    /**
     * @param \PSX\Api\Resource $resource
     */
    public function set(Resource $resource)
    {
        $this->offsetSet($resource->getPath(), $resource);
    }

    /**
     * @param string $path
     * @return boolean
     */
    public function has(string $path)
    {
        return $this->offsetExists($path);
    }

    /**
     * @param string $path
     * @return \PSX\Api\Resource
     */
    public function get(string $path)
    {
        return $this->offsetExists($path) ? $this->offsetGet($path) : null;
    }

    /**
     * @param FilterInterface $filter
     * @return ResourceCollection
     */
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
