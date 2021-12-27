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

namespace PSX\Api\Builder;

use PSX\Api\Resource\MethodAbstract;
use PSX\Schema\Builder;
use PSX\Schema\Exception\InvalidSchemaException;

/**
 * MethodBuilderInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface MethodBuilderInterface
{
    public function setOperationId(?string $operationId): void;

    public function setDescription(?string $description): void;

    public function setQueryParameters(?string $typeName): Builder;

    /**
     * @throws InvalidSchemaException
     */
    public function setRequest(string $schemaName): void;

    /**
     * @throws InvalidSchemaException
     */
    public function addResponse(int $statusCode, string $schemaName): void;

    public function setSecurity(string $name, array $scopes): void;

    public function setTags(array $tags): void;

    public function getMethod(): MethodAbstract;
}
