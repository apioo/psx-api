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
     * @param \PSX\Api\Listing\FilterInterface|null $filter
     * @return \PSX\Api\Listing\Route[]
     */
    public function getAvailableRoutes(FilterInterface $filter = null): iterable;

    /**
     * Returns a specification for a specific resource path
     *
     * @param string $path
     * @param integer|null $version
     * @return \PSX\Api\SpecificationInterface|null
     */
    public function find(string $path, ?int $version = null): ?SpecificationInterface;

    /**
     * Returns all available resources
     *
     * @param integer|null $version
     * @param \PSX\Api\Listing\FilterInterface|null $filter
     * @return \PSX\Api\SpecificationInterface
     */
    public function findAll(?int $version = null, FilterInterface $filter = null): SpecificationInterface;
}
