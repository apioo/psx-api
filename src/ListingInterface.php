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
use PSX\Api\Listing\Route;

/**
 * A listing knows all API endpoints in a system and can be used to get resource
 * definitions for specific endpoints or to get an index of all available
 * endpoints
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface ListingInterface
{
    /**
     * Returns all available routes
     *
     * @param FilterInterface|null $filter
     * @return Route[]
     */
    public function getAvailableRoutes(?FilterInterface $filter = null): iterable;

    /**
     * Returns a specification for a specific resource path
     *
     * @param string $path
     * @param string|null $version
     * @return SpecificationInterface|null
     */
    public function find(string $path, ?string $version = null): ?SpecificationInterface;

    /**
     * Returns all available resources
     *
     * @param string|null $version
     * @param FilterInterface|null $filter
     * @return SpecificationInterface
     */
    public function findAll(?string $version = null, FilterInterface $filter = null): SpecificationInterface;
}
