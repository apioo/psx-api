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

namespace PSX\Api\Builder;

use PSX\Api\SecurityInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\ContentType;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Type\DefinitionTypeAbstract;
use PSX\Schema\Type\PropertyTypeAbstract;

/**
 * SpecificationBuilderInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface SpecificationBuilderInterface
{
    public function setBaseUrl(string $baseUrl): void;

    public function setSecurity(SecurityInterface $security): void;

    public function addOperation(string $operationId, string $method, string $path, int $statusCode, PropertyTypeAbstract|ContentType $schema): OperationBuilderInterface;

    public function addDefinitions(DefinitionsInterface $definitions): self;

    public function addType(string $name, DefinitionTypeAbstract $schema): self;

    public function getSpecification(): SpecificationInterface;
}
