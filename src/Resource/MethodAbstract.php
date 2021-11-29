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

    private ?string $operationId = null;
    private ?string $description = null;
    private ?string $queryParameters = null;
    private ?string $request = null;
    private array $responses;
    private ?array $security = null;

    public function __construct()
    {
        $this->responses = [];
    }

    public function setOperationId(?string $operationId): void
    {
        $this->operationId = $operationId;
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setQueryParameters(?string $typeName): void
    {
        $this->queryParameters = $typeName;
    }

    public function getQueryParameters(): ?string
    {
        return $this->queryParameters;
    }

    public function hasQueryParameters(): bool
    {
        return !empty($this->queryParameters);
    }

    public function setRequest(?string $typeName): void
    {
        $this->request = $typeName;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function hasRequest(): bool
    {
        return !empty($this->request);
    }

    public function addResponse(int $statusCode, string $typeName): void
    {
        $this->responses[$statusCode] = $typeName;
    }

    public function getResponses(): array
    {
        return $this->responses;
    }

    public function getResponse(int $statusCode): ?string
    {
        if (isset($this->responses[$statusCode])) {
            return $this->responses[$statusCode];
        } else {
            throw new RuntimeException('Status code response ' . $statusCode . ' is not available for this resource');
        }
    }

    public function hasResponse(int $statusCode): bool
    {
        return isset($this->responses[$statusCode]);
    }

    public function setSecurity(string $name, array $scopes): void
    {
        $this->security[$name] = $scopes;
    }

    public function getSecurity(): ?array
    {
        return $this->security;
    }

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
