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

namespace PSX\Api;

use ArrayIterator;
use IteratorAggregate;
use PSX\Api\Resource\MethodAbstract;
use PSX\Schema\Property;
use PSX\Schema\PropertyInterface;

/**
 * A resource describes the capabilities of an API endpoint
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Resource implements IteratorAggregate
{
    const STATUS_ACTIVE      = 0x1;
    const STATUS_DEPRECATED  = 0x2;
    const STATUS_CLOSED      = 0x3;
    const STATUS_DEVELOPMENT = 0x4;

    const CODE_INFORMATIONAL = 199;
    const CODE_SUCCESS       = 299;
    const CODE_REDIRECTION   = 399;
    const CODE_CLIENT_ERROR  = 499;
    const CODE_SERVER_ERROR  = 599;

    /**
     * @var integer
     */
    protected $status;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var \PSX\Schema\PropertyInterface
     */
    protected $pathParameters;

    /**
     * @var array
     */
    protected $methods;

    /**
     * @param integer $status
     * @param string $path
     */
    public function __construct($status, $path)
    {
        $this->status          = $status;
        $this->path            = $path;
        $this->pathParameters  = Property::getObject()->setTitle('path');
        $this->methods         = [];
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * @return boolean
     */
    public function isDeprecated()
    {
        return $this->status == self::STATUS_DEPRECATED;
    }

    /**
     * @return boolean
     */
    public function isClosed()
    {
        return $this->status == self::STATUS_CLOSED;
    }

    /**
     * @return boolean
     */
    public function isDevelopment()
    {
        return $this->status == self::STATUS_DEVELOPMENT;
    }

    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function addPathParameter($name, PropertyInterface $property = null)
    {
        $this->pathParameters->addProperty($name, $property);

        return $this;
    }

    public function getPathParameters()
    {
        return $this->pathParameters;
    }

    public function hasPathParameters()
    {
        return count($this->pathParameters->getProperties() ?: []) > 0;
    }

    public function addMethod(MethodAbstract $method)
    {
        $this->methods[$method->getName()] = $method;
    }

    /**
     * @param string $method
     * @return \PSX\Api\Resource\MethodAbstract
     */
    public function getMethod($method)
    {
        if (isset($this->methods[$method])) {
            return $this->methods[$method];
        } else {
            throw new \RuntimeException('Method ' . $method . ' is not available for this resource');
        }
    }

    /**
     * @return \PSX\Api\Resource\MethodAbstract[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return array_keys($this->methods);
    }

    /**
     * @param string $method
     * @return boolean
     */
    public function hasMethod($method)
    {
        return isset($this->methods[$method]);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->methods);
    }
}
