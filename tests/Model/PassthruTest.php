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

namespace PSX\Api\Tests\Inspector;

use PHPUnit\Framework\TestCase;
use PSX\Api\Model\Passthru;
use PSX\Record\Record;
use PSX\Record\RecordInterface;

/**
 * PassthruTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PassthruTest extends TestCase
{
    public function testGet()
    {
        $object = Record::fromArray(['foo' => 'bar']);
        $record = Passthru::fromPayload($object);

        $this->assertEquals('bar', $record->get('foo'));
        $this->assertEquals('bar', $record['foo']);
        $this->assertInstanceOf(RecordInterface::class, $record->getPayload());
    }

    public function testGetNested()
    {
        $object = Record::fromArray(['foo' => (object) ['foo' => 'bar']]);
        $record = Passthru::fromPayload($object);

        $this->assertEquals('bar', $record->get('foo.foo'));
        $this->assertInstanceOf(RecordInterface::class, $record->getPayload());
    }

    public function testGetArray()
    {
        $object = Record::fromArray(['foo' => ['foo', 'bar']]);
        $record = Passthru::fromPayload($object);

        $this->assertEquals('bar', $record->get('foo[1]'));
        $this->assertInstanceOf(RecordInterface::class, $record->getPayload());
    }
}
