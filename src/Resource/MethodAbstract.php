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

namespace PSX\Api\Resource;

use PSX\Schema\Property;
use PSX\Schema\PropertyInterface;
use PSX\Schema\SchemaInterface;
use RuntimeException;

/**
 * MethodAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class MethodAbstract
{
    /**
     * @var string
     */
    protected $operationId;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var \PSX\Schema\PropertyInterface
     */
    protected $queryParameters;

    /**
     * @var \PSX\Schema\SchemaInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $responses;

    /**
     * @var array
     */
    protected $security;

    public function __construct()
    {
        $this->queryParameters = Property::getObject()->setTitle('query');
        $this->responses       = [];
    }

    public function setOperationId($operationId)
    {
        $this->operationId = $operationId;

        return $this;
    }

    public function getOperationId()
    {
        return $this->operationId;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function addQueryParameter($name, PropertyInterface $property = null)
    {
        $this->queryParameters->addProperty($name, $property);

        return $this;
    }

    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    public function hasQueryParameters()
    {
        return count($this->queryParameters->getProperties() ?: []) > 0;
    }

    public function setRequest(SchemaInterface $schema)
    {
        $this->request = $schema;

        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function hasRequest()
    {
        return $this->request instanceof SchemaInterface;
    }

    public function addResponse($statusCode, SchemaInterface $schema)
    {
        $this->responses[$statusCode] = $schema;

        return $this;
    }

    public function getResponses()
    {
        return $this->responses;
    }

    public function getResponse($statusCode)
    {
        if (isset($this->responses[$statusCode])) {
            return $this->responses[$statusCode];
        } else {
            throw new RuntimeException('Status code response ' . $statusCode . ' is not available for this resource');
        }
    }

    public function hasResponse($statusCode)
    {
        return isset($this->responses[$statusCode]);
    }

    public function setSecurity($name, array $scopes)
    {
        $this->security[$name] = $scopes;

        return $this;
    }

    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * Returns the uppercase name of the method
     *
     * @return string
     */
    abstract public function getName();
}
