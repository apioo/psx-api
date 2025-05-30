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

namespace PSX\Api\Tests\Parser\Attribute;

use PSX\Api\Attribute\Body;
use PSX\Api\Attribute\Description;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Header;
use PSX\Api\Attribute\OperationId;
use PSX\Api\Attribute\Param;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Query;
use PSX\Api\Tests\Parser\Model\Incoming;
use PSX\Api\Tests\Parser\Model\Outgoing;
use PSX\DateTime\DateTime;
use PSX\DateTime\DayOfWeek;

/**
 * PropertyController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class PropertyController
{
    #[Get]
    #[Path('/foo/:fooId')]
    #[OperationId('my.operation')]
    #[Description('Test description')]
    protected function doGet(
        #[Header('Content-Type')] string $contentType,
        #[Param] string $fooId,
        #[Query] string $foo,
        #[Query] int $integer,
        #[Query] float $number,
        #[Query] DateTime $date,
        #[Query] bool $boolean,
        #[Query] string $string,
        #[Query] DayOfWeek $dayOfWeek,
        #[Body] Incoming $body): Outgoing
    {
        return new Outgoing('foo');
    }
}
