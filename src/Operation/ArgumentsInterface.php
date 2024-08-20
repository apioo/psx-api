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

namespace PSX\Api\Operation;

/**
 * ArgumentsInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface ArgumentsInterface
{
    public function has(string $name): bool;

    public function get(string $name): Argument;

    /**
     * @return array<string, Argument>
     */
    public function getAll(): array;

    /**
     * Returns all arguments with a specific in type i.e. query, header, body
     *
     * @return array<string, Argument>
     */
    public function getAllIn(string $in): array;

    public function getFirstIn(string $in): ?Argument;

    public function isEmpty(): bool;
}
