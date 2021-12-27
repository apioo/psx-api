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

namespace PSX\Api\Parser\Attribute;

use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Exclude;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Method;
use PSX\Api\Attribute\MethodAbstract;
use PSX\Api\Attribute\OperationId;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\QueryParam;
use PSX\Api\Attribute\Security;
use PSX\Api\Attribute\Tags;

/**
 * Meta
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Meta
{
    private ?Description $description = null;
    private ?Exclude $exclude = null;
    private ?MethodAbstract $method = null;
    private ?Path $path = null;
    /**
     * @var PathParam[]
     */
    private array $pathParams = [];
    /**
     * @var QueryParam[]
     */
    private array $queryParams = [];
    private ?Incoming $incoming = null;
    /**
     * @var Outgoing[]
     */
    private array $outgoing = [];
    private ?OperationId $operationId = null;
    private ?Tags $tags = null;
    private ?Security $security = null;

    public function __construct(array $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($attribute instanceof Exclude) {
                $this->exclude = $attribute;
            } elseif ($attribute instanceof Description) {
                $this->description = $attribute;
            } elseif ($attribute instanceof MethodAbstract) {
                $this->method = $attribute;
            } elseif ($attribute instanceof Path) {
                $this->path = $attribute;
            } elseif ($attribute instanceof PathParam) {
                $this->pathParams[] = $attribute;
            } elseif ($attribute instanceof QueryParam) {
                $this->queryParams[] = $attribute;
            } elseif ($attribute instanceof Incoming) {
                $this->incoming = $attribute;
            } elseif ($attribute instanceof Outgoing) {
                $this->outgoing[$attribute->code] = $attribute;
            } elseif ($attribute instanceof OperationId) {
                $this->operationId = $attribute;
            } elseif ($attribute instanceof Tags) {
                $this->tags = $attribute;
            } elseif ($attribute instanceof Security) {
                $this->security = $attribute;
            }
        }
    }

    public function merge(Meta $meta)
    {
        if ($this->exclude == null) {
            $this->exclude = $meta->getExclude();
        }

        if ($this->description == null) {
            $this->description = $meta->getDescription();
        }

        if ($this->method == null) {
            $this->method = $meta->getMethod();
        }

        if ($this->path == null) {
            $this->path = $meta->getPath();
        }

        $this->pathParams = array_merge($this->pathParams, $meta->getPathParams());
        $this->queryParams = array_merge($this->queryParams, $meta->getQueryParams());

        if ($this->incoming == null) {
            $this->incoming = $meta->getIncoming();
        }

        $this->outgoing = array_merge($this->outgoing, $meta->getOutgoing());

        if ($this->operationId == null) {
            $this->operationId = $meta->getOperationId();
        }

        if ($this->tags == null) {
            $this->tags = $meta->getTags();
        }

        if ($this->security == null) {
            $this->security = $meta->getSecurity();
        }
    }

    public function getDescription(): ?Description
    {
        return $this->description;
    }

    public function getExclude(): ?Exclude
    {
        return $this->exclude;
    }

    public function getMethod(): ?MethodAbstract
    {
        return $this->method;
    }

    public function hasMethod(): bool
    {
        return $this->method instanceof MethodAbstract;
    }

    public function getPath(): ?Path
    {
        return $this->path;
    }

    public function hasPath(): bool
    {
        return $this->path instanceof Path;
    }

    public function getPathParams(): array
    {
        return $this->pathParams;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getIncoming(): ?Incoming
    {
        return $this->incoming;
    }

    public function setIncoming(Incoming $incoming): void
    {
        $this->incoming = $incoming;
    }

    public function hasIncoming(): bool
    {
        return $this->incoming instanceof Incoming;
    }

    public function getOutgoing(): array
    {
        return $this->outgoing;
    }

    public function addOutgoing(Outgoing $outgoing): void
    {
        $this->outgoing[] = $outgoing;
    }

    public function hasOutgoing(): bool
    {
        return count($this->outgoing) > 0;
    }

    public function getOperationId(): ?OperationId
    {
        return $this->operationId;
    }

    public function getTags(): ?Tags
    {
        return $this->tags;
    }

    public function getSecurity(): ?Security
    {
        return $this->security;
    }

    public function isExcluded(): bool
    {
        return $this->exclude instanceof Exclude;
    }

    public static function fromAttributes(array $attributes): static
    {
        $result = [];
        foreach ($attributes as $attribute) {
            $result[] = $attribute->newInstance();
        }

        return new static($result);
    }
}
