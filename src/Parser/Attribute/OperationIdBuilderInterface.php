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

namespace PSX\Api\Parser\Attribute;

use PSX\Api\Attribute\Authorization;
use PSX\Api\Attribute\Deprecated;
use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Exclude;
use PSX\Api\Attribute\HeaderParam;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\MethodAbstract;
use PSX\Api\Attribute\OperationId;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\PathParam;
use PSX\Api\Attribute\QueryParam;
use PSX\Api\Attribute\StatusCode;
use PSX\Api\Attribute\Security;
use PSX\Api\Attribute\Tags;

/**
 * OperationIdBuilder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface OperationIdBuilderInterface
{
    public function build(string $controllerClass, string $methodName): string;
}
