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

namespace PSX\Api\Resource;

use PSX\Api\TagableTrait;
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
    use TagableTrait;

    /**
     * @var string
     */
    protected $operationId;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $queryParameters;

    /**
     * @var string
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
        $this->responses = [];
    }

    /**
     * @param string $operationId
     */
    public function setOperationId(?string $operationId)
    {
        $this->operationId = $operationId;
    }

    /**
     * @return string
     */
    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    /**
     * @param string $description
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $typeName
     */
    public function setQueryParameters(?string $typeName)
    {
        $this->queryParameters = $typeName;
    }

    /**
     * @return string
     */
    public function getQueryParameters(): ?string
    {
        return $this->queryParameters;
    }

    /**
     * @return bool
     */
    public function hasQueryParameters(): bool
    {
        return !empty($this->queryParameters);
    }

    /**
     * @param string $typeName
     */
    public function setRequest(?string $typeName)
    {
        $this->request = $typeName;
    }

    /**
     * @return string
     */
    public function getRequest(): ?string
    {
        return $this->request;
    }

    /**
     * @return bool
     */
    public function hasRequest(): bool
    {
        return !empty($this->request);
    }

    /**
     * @param integer $statusCode
     * @param string $typeName
     */
    public function addResponse(int $statusCode, string $typeName)
    {
        $this->responses[$statusCode] = $typeName;
    }

    /**
     * @return array
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @param int $statusCode
     * @return string
     */
    public function getResponse(int $statusCode): ?string
    {
        if (isset($this->responses[$statusCode])) {
            return $this->responses[$statusCode];
        } else {
            throw new RuntimeException('Status code response ' . $statusCode . ' is not available for this resource');
        }
    }

    /**
     * @param int $statusCode
     * @return bool
     */
    public function hasResponse($statusCode): bool
    {
        return isset($this->responses[$statusCode]);
    }

    /**
     * @param string $name
     * @param array $scopes
     */
    public function setSecurity(string $name, array $scopes)
    {
        $this->security[$name] = $scopes;
    }

    /**
     * @return array
     */
    public function getSecurity(): ?array
    {
        return $this->security;
    }

    /**
     * @return bool
     */
    public function hasSecurity(): bool
    {
        return !empty($this->security);
    }

    /**
     * Returns the uppercase name of the method
     *
     * @return string
     */
    abstract public function getName(): string;

    public function toArray(): array
    {
        $responses = [];
        foreach ($this->responses as $statusCode => $response) {
            $responses[$statusCode] = $response;
        }

        return array_filter([
            'operationId' => $this->operationId,
            'description' => $this->description,
            'security' => $this->security,
            'tags' => $this->tags,
            'queryParameters' => $this->queryParameters,
            'request' => $this->request,
            'responses' => $responses,
        ], function($value){
            return $value !== null;
        });
    }
}
