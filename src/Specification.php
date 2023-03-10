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
    private OperationsInterface $operations;
    private DefinitionsInterface $definitions;
    private ?SecurityInterface $security;
    private ?string $baseUrl;

    public function __construct(?OperationsInterface $operations = null, ?DefinitionsInterface $definitions = null, ?SecurityInterface $security = null, ?string $baseUrl = null)
    {
        $this->operations = $operations ?? new Operations();
        $this->definitions = $definitions ?? new Definitions();
        $this->security = $security;
        $this->baseUrl = $baseUrl;
    }

    public function getOperations(): OperationsInterface
    {
        return $this->operations;
    }

    public function getDefinitions(): DefinitionsInterface
    {
        return $this->definitions;
    }

    public function getSecurity(): ?SecurityInterface
    {
        return $this->security;
    }

    public function setSecurity(SecurityInterface $security): void
    {
        $this->security = $security;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(?string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function merge(SpecificationInterface $specification): void
    {
        $this->operations->merge($specification->getOperations());
        $this->definitions->merge($specification->getDefinitions());
    }

    public function jsonSerialize(): array
    {
        return [
            'baseUrl' => $this->baseUrl,
            'security' => $this->security,
            'operations' => $this->operations,
            'definitions' => $this->definitions,
        ];
    }
}
