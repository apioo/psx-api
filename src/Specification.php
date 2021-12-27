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

use PSX\Schema\Definitions;
use PSX\Schema\DefinitionsInterface;

/**
 * Specification
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Specification implements SpecificationInterface, \JsonSerializable
{
    private ResourceCollection $resourceCollection;
    private DefinitionsInterface $definitions;
    private ?SecurityInterface $security;

    public function __construct(?ResourceCollection $resourceCollection = null, ?DefinitionsInterface $definitions = null, ?SecurityInterface $security = null)
    {
        $this->resourceCollection = $resourceCollection ?? new ResourceCollection();
        $this->definitions = $definitions ?? new Definitions();
        $this->security = $security;
    }

    public function getResourceCollection(): ResourceCollection
    {
        return $this->resourceCollection;
    }

    public function getDefinitions(): DefinitionsInterface
    {
        return $this->definitions;
    }

    public function get(string $path): ?SpecificationInterface
    {
        $resource = $this->getResourceCollection()->get($path);
        if (!$resource instanceof Resource) {
            return null;
        }

        return new Specification(
            new ResourceCollection([$resource]),
            $this->definitions
        );
    }

    public function getSecurity(): ?SecurityInterface
    {
        return $this->security;
    }

    public function setSecurity(SecurityInterface $security): void
    {
        $this->security = $security;
    }

    public function merge(SpecificationInterface $specification): void
    {
        foreach ($specification->getResourceCollection() as $path => $resource) {
            if ($this->resourceCollection->has($path)) {
                foreach ($resource->getMethods() as $method) {
                    $this->resourceCollection->get($path)->addMethod($method);
                }
            } else {
                $this->resourceCollection->set($resource);
            }
        }

        $this->definitions->merge($specification->getDefinitions());
    }

    public function jsonSerialize(): array
    {
        return [
            'security' => $this->security,
            'resources' => $this->resourceCollection,
            'definitions' => $this->definitions,
        ];
    }

    public static function fromResource(Resource $resource, DefinitionsInterface $definitions): self
    {
        $collection = new ResourceCollection();
        $collection->set($resource);

        return new static($collection, $definitions);
    }
}
