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

use PSX\Api\Exception\InvalidArgumentException;
use PSX\Api\OperationInterface;
use PSX\Schema\ContentType;
use PSX\Schema\TypeInterface;

/**
 * ResourceBuilderInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface OperationBuilderInterface
{
    public function setDescription(string $description): self;

    /**
     * @throws InvalidArgumentException
     */
    public function addArgument(string $name, string $in, TypeInterface|ContentType $schema): self;

    public function setAuthorization(bool $authorization): self;

    public function setSecurity(array $security): self;

    public function setStability(int $stability): self;

    /**
     * @throws InvalidArgumentException
     */
    public function addThrow(int $statusCode, TypeInterface|ContentType $schema): self;

    public function setTags(array $tags): self;

    public function getOperation(): OperationInterface;
}
