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

/**
 * FilterFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FilterFactory implements FilterFactoryInterface
{
    /**
     * @var array
     */
    protected $container;

    /**
     * @var string
     */
    protected $default;

    public function __construct()
    {
        $this->container = [];
        $this->default   = null;
    }

    /**
     * @param string $name
     * @param \PSX\Api\Listing\FilterInterface $filter
     */
    public function addFilter($name, FilterInterface $filter)
    {
        $this->container[$name] = $filter;
    }

    /**
     * @param string $name
     */
    public function setDefault($name)
    {
        $this->default = $name;
    }

    /**
     * @inheritdoc
     */
    public function getFilter($name)
    {
        if (isset($this->container[$name])) {
            return $this->container[$name];
        } elseif ($this->default !== null) {
            return $this->container[$this->default] ?? null;
        } else {
            return null;
        }
    }
}
