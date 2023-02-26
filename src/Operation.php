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

namespace PSX\Api;

use PSX\Api\Operation\Argument;
use PSX\Api\Operation\Arguments;
use PSX\Api\Operation\Response;

/**
 * Operation
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Operation implements OperationInterface, \JsonSerializable
{
    private string $method;
    private string $path;
    private Response $return;

    private string $description = '';
    private Arguments $arguments;
    private bool $authorization = true;
    private array $security = [];
    private int $stability = self::STABILITY_EXPERIMENTAL;
    private array $throws = [];
    private array $tags = [];

    public function __construct(string $method, string $path, Response $return)
    {
        $this->method = $method;
        $this->path = $path;
        $this->return = $return;
        $this->arguments = new Arguments();
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getReturn(): Response
    {
        return $this->return;
    }

    public function setReturn(Response $return): void
    {
        $this->return = $return;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getArguments(): Arguments
    {
        return $this->arguments;
    }

    public function setArguments(Arguments $arguments): void
    {
        $this->arguments = $arguments;
    }

    public function hasAuthorization(): bool
    {
        return $this->authorization;
    }

    public function setAuthorization(bool $authorization): void
    {
        $this->authorization = $authorization;
    }

    public function getSecurity(): array
    {
        return $this->security;
    }

    /**
     * An array of scopes
     */
    public function setSecurity(array $security): void
    {
        $this->security = $security;
    }

    public function getStability(): int
    {
        return $this->stability;
    }

    public function setStability(int $stability): void
    {
        $this->stability = $stability;
    }

    /**
     * @return array<Response>
     */
    public function getThrows(): array
    {
        return $this->throws;
    }

    /**
     * @param array<Response> $throws
     */
    public function setThrows(array $throws): void
    {
        $this->throws = $throws;
    }

    public function addThrow(Response $response): void
    {
        $this->throws[] = $response;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'path' => $this->path,
            'method' => $this->method,
            'return' => $this->return,
            'arguments' => $this->arguments,
            'throws' => $this->throws,
            'description' => $this->description,
            'stability' => $this->stability,
            'security' => $this->security,
            'authorization' => $this->authorization,
            'tags' => $this->tags,
        ], static function ($value) {
            return $value !== null;
        });
    }
}
