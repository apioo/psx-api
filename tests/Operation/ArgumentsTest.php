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

namespace PSX\Api\Tests\Operation;

use PHPUnit\Framework\TestCase;
use PSX\Api\Operation;
use PSX\Api\Operations;
use PSX\Schema\TypeFactory;

/**
 * ArgumentsTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ArgumentsTest extends TestCase
{
    public function testArguments()
    {
        $arguments = new Operation\Arguments();
        $arguments->add('payload', new Operation\Argument(Operation\ArgumentInterface::IN_BODY, TypeFactory::getAny()));
        $arguments->add('my_header', new Operation\Argument(Operation\ArgumentInterface::IN_HEADER, TypeFactory::getAny()));
        $arguments->add('startIndex', new Operation\Argument(Operation\ArgumentInterface::IN_QUERY, TypeFactory::getAny()));
        $arguments->add('count', new Operation\Argument(Operation\ArgumentInterface::IN_QUERY, TypeFactory::getAny()));
        $arguments->add('id', new Operation\Argument(Operation\ArgumentInterface::IN_PATH, TypeFactory::getAny()));

        $this->assertEquals(5, count($arguments->getAll()));
        $this->assertEquals(1, count($arguments->getAllIn(Operation\ArgumentInterface::IN_BODY)));
        $this->assertEquals(1, count($arguments->getAllIn(Operation\ArgumentInterface::IN_HEADER)));
        $this->assertEquals(2, count($arguments->getAllIn(Operation\ArgumentInterface::IN_QUERY)));
        $this->assertEquals(1, count($arguments->getAllIn(Operation\ArgumentInterface::IN_PATH)));
        $this->assertInstanceOf(Operation\Argument::class, $arguments->getFirstIn(Operation\ArgumentInterface::IN_BODY));
        $this->assertFalse($arguments->isEmpty());
    }
}
