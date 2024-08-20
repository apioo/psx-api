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

use PSX\Api\Operation\Arguments;
use PSX\Api\Operation\Response;

/**
 * OperationInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface OperationInterface
{
    public const STABILITY_DEPRECATED = 0;
    public const STABILITY_EXPERIMENTAL = 1;
    public const STABILITY_STABLE = 2;
    public const STABILITY_LEGACY = 3;

    public function getMethod(): string;
    public function getPath(): string;
    public function getReturn(): Response;
    public function getArguments(): Arguments;

    /**
     * @return array<Response>
     */
    public function getThrows(): array;
    public function getDescription(): string;
    public function getStability(): int;
    public function getSecurity(): array;
    public function hasAuthorization(): bool;

    /**
     * @return array<string>
     */
    public function getTags(): array;
}
