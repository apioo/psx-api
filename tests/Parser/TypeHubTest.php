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

namespace PSX\Api\Tests\Parser;

use PSX\Api\OperationInterface;
use PSX\Api\Tests\ApiManagerTestCase;
use PSX\Schema\Type\StructDefinitionType;

/**
 * TypeHubTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TypeHubTest extends ApiManagerTestCase
{
    public function testParseSimple()
    {
        $specification = $this->apiManager->getApi('typehub://fusio:marketplace@0.2.1');
        $definitions = $specification->getDefinitions();
        $operation = $specification->getOperations()->get('marketplace.directory.action.get');

        $this->assertInstanceOf(OperationInterface::class, $operation);
        $this->assertEquals('GET', $operation->getMethod());
        $this->assertEquals('/marketplace/directory/action/:user/:name', $operation->getPath());
        $this->assertEquals('Returns details of a specific action', $operation->getDescription());

        $arguments = $operation->getArguments();
        $this->assertEquals('path', $arguments->get('user')->getIn());
        $this->assertEquals(['type' => 'string'], $arguments->get('user')->getSchema()->toArray());
        $this->assertEquals('path', $arguments->get('name')->getIn());
        $this->assertEquals(['type' => 'string'], $arguments->get('name')->getSchema()->toArray());

        $this->assertEquals(200, $operation->getReturn()->getCode());
        $this->assertEquals(['type' => 'reference', 'target' => 'MarketplaceAction'], $operation->getReturn()->getSchema()->toArray());

        $this->assertCount(1, $operation->getThrows());
        $this->assertEquals(999, $operation->getThrows()[0]->getCode());
        $this->assertEquals(['type' => 'reference', 'target' => 'MarketplaceMessage'], $operation->getThrows()[0]->getSchema()->toArray());

        $this->assertInstanceOf(StructDefinitionType::class, $definitions->getType('MarketplaceAction'));
        $this->assertInstanceOf(StructDefinitionType::class, $definitions->getType('MarketplaceMessage'));
    }
}
