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

    const STATUS_ACTIVE      = 0x1;
    const STATUS_DEPRECATED  = 0x2;
    const STATUS_CLOSED      = 0x3;
    const STATUS_DEVELOPMENT = 0x4;

    const CODE_INFORMATIONAL = 199;
    const CODE_SUCCESS       = 299;
    const CODE_REDIRECTION   = 399;
    const CODE_CLIENT_ERROR  = 499;
    const CODE_SERVER_ERROR  = 599;

    private int $status;
    private string $path;
    private ?string $description = null;
    private ?string $pathParameters = null;

    /**
     * @var MethodAbstract[]
     */
    private array $methods;

    /**
     * @param integer $status
     * @param string $path
     */
    public function __construct(int $status, string $path)
    {
        $this->status  = $status;
        $this->path    = $path;
        $this->methods = [];
    }

    /**
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * @return boolean
     */
    public function isDeprecated(): bool
    {
        return $this->status == self::STATUS_DEPRECATED;
    }

    /**
     * @return boolean
     */
    public function isClosed(): bool
    {
        return $this->status == self::STATUS_CLOSED;
    }

    /**
     * @return boolean
     */
    public function isDevelopment(): bool
    {
        return $this->status == self::STATUS_DEVELOPMENT;
    }

    /**
     * @return integer
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $typeName
     */
    public function setPathParameters(?string $typeName): void
    {
        $this->pathParameters = $typeName;
    }

    /**
     * @return string|null
     */
    public function getPathParameters(): ?string
    {
        return $this->pathParameters;
    }

    /**
     * @return bool
     */
    public function hasPathParameters(): bool
    {
        return !empty($this->pathParameters);
    }

    /**
     * @param MethodAbstract $method
     */
    public function addMethod(MethodAbstract $method)
    {
        $this->methods[$method->getName()] = $method;
    }

    /**
     * @param string $method
     * @return MethodAbstract
     */
    public function getMethod(string $method): ?MethodAbstract
    {
        if (isset($this->methods[$method])) {
            return $this->methods[$method];
        } else {
            throw new \RuntimeException('Method ' . $method . ' is not available for this resource');
        }
    }

    /**
     * @return MethodAbstract[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return array_keys($this->methods);
    }

    /**
     * @param string $method
     * @return boolean
     */
    public function hasMethod(string $method): bool
    {
        return isset($this->methods[$method]);
    }

    /**
     * @inheritdoc
     */
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

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
