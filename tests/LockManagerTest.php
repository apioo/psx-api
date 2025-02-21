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
use PSX\Api\Exception\BreakingChangesException;
use PSX\Api\LockManager;
use PSX\Api\Operation;
use PSX\Api\Operations;
use PSX\Api\SpecificationInterface;
use PSX\Schema\SchemaManager;
use PSX\Schema\TypeFactory;

/**
 * LockManagerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class LockManagerTest extends ApiManagerTestCase
{
    public function testVerifyBreakingChanges(): void
    {
        $lockManager = new LockManager(new SchemaManager());

        $specification = $this->apiManager->getApi(__DIR__ . '/Parser/typeapi/simple.json');

        $lockFile = __DIR__ . '/api.lock';
        $lockManager->lock($specification, $lockFile);

        try {
            $specification = $this->apiManager->getApi(__DIR__ . '/Parser/typeapi/simple_bc.json');

            $lockManager->verify($specification, $lockFile);

            $this->fail('Must throw a breaking change exception');
        } catch (BreakingChangesException $e) {
            $this->assertSame(3, count($e->getChanges()));
            $this->assertSame([
                'Operation "test.get.arguments.integer" was removed',
                'Property "Rating.text" was removed',
                'Property "Song.length" was removed',
            ], $e->getChanges());
        }
    }

    public function testVerifyNoBreakingChanges(): void
    {
        $lockManager = new LockManager(new SchemaManager());

        $specification = $this->apiManager->getApi(__DIR__ . '/Parser/typeapi/simple.json');

        $lockFile = __DIR__ . '/api.lock';
        $lockManager->lock($specification, $lockFile);

        $specification = $this->apiManager->getApi(__DIR__ . '/Parser/typeapi/simple.json');

        $lockManager->verify($specification, $lockFile);

        $this->assertInstanceOf(SpecificationInterface::class, $specification);
    }
}
