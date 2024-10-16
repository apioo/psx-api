<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Schema\DefinitionsInterface;

/**
 * SpecificationInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface SpecificationInterface
{
    /**
     * Returns all operations assigned to this specification
     */
    public function getOperations(): OperationsInterface;

    /**
     * Returns all definitions assigned to this specification
     */
    public function getDefinitions(): DefinitionsInterface;

    /**
     * Returns the configured security definition
     */
    public function getSecurity(): ?SecurityInterface;

    /**
     * Returns the configured base url
     */
    public function getBaseUrl(): ?string;

    /**
     * Merges all operations and definitions into the specification
     */
    public function merge(SpecificationInterface $specification): void;
}
