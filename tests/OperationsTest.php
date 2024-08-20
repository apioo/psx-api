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

namespace PSX\Api\Tests;

use PHPUnit\Framework\TestCase;
use PSX\Api\Operation;
use PSX\Api\Operations;
use PSX\Schema\TypeFactory;

/**
 * OperationsTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OperationsTest extends TestCase
{
    public function testCollection()
    {
        $operations = new Operations();
        $operation  = new Operation('GET', '/foo', new Operation\Response(200, TypeFactory::getReference('My_Type')));

        $this->assertFalse($operations->has('my.operation'));

        $operations->add('my.operation', $operation);

        $this->assertInstanceOf(Operation::class, $operations->get('my.operation'));
        $this->assertTrue($operations->has('my.operation'));
    }
}
