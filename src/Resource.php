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

use ArrayIterator;
use IteratorAggregate;
use PSX\Api\Resource\MethodAbstract;

/**
 * A resource describes the capabilities of an API endpoint
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Resource implements IteratorAggregate, \JsonSerializable
{
    use TagableTrait;

    public const STATUS_ACTIVE      = 0x1;
    public const STATUS_DEPRECATED  = 0x2;
    public const STATUS_CLOSED      = 0x3;
    public const STATUS_DEVELOPMENT = 0x4;

    public const CODE_INFORMATIONAL = 199;
    public const CODE_SUCCESS       = 299;
    public const CODE_REDIRECTION   = 399;
    public const CODE_CLIENT_ERROR  = 499;
    public const CODE_SERVER_ERROR  = 599;

    private int $status;
    private string $path;
    private ?string $description = null;
    private ?string $pathParameters = null;

    /**
     * @var MethodAbstract[]
     */
    private array $methods;

    public function __construct(int $status, string $path)
    {
        $this->status  = $status;
        $this->path    = $path;
        $this->methods = [];
    }

    public function isActive(): bool
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function isDeprecated(): bool
    {
        return $this->status == self::STATUS_DEPRECATED;
    }

    public function isClosed(): bool
    {
        return $this->status == self::STATUS_CLOSED;
    }

    public function isDevelopment(): bool
    {
        return $this->status == self::STATUS_DEVELOPMENT;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setPathParameters(?string $typeName): void
    {
        $this->pathParameters = $typeName;
    }

    public function getPathParameters(): ?string
    {
        return $this->pathParameters;
    }

    public function hasPathParameters(): bool
    {
        return !empty($this->pathParameters);
    }

    public function addMethod(MethodAbstract $method)
    {
        $this->methods[$method->getName()] = $method;
    }

    public function getMethod(string $method): ?MethodAbstract
    {
        if (isset($this->methods[$method])) {
            return $this->methods[$method];
        } else {
            throw new \RuntimeException('Method ' . $method . ' is not available for this resource');
        }
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getAllowedMethods(): array
    {
        return array_keys($this->methods);
    }

    public function hasMethod(string $method): bool
    {
        return isset($this->methods[$method]);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->methods);
    }

    public function toArray(): array
    {
        $methods = [];
        foreach ($this->methods as $methodName => $method) {
            $methods[$methodName] = $method->toArray();
        }

        return array_filter([
            'status' => $this->status,
            'path' => $this->path,
            'description' => $this->description,
            'pathParameters' => $this->pathParameters,
            'methods' => $methods,
        ], function($value){
            return $value !== null;
        });
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
