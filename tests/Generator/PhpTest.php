<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Api\Tests\Generator;

use PSX\Api\Generator\Php;

/**
 * PhpTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PhpTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new Php();
        $php       = $generator->generate($this->getResource());

        $expect = <<<'PHP'
<?php

namespace PSX\Generation;

use PSX\Framework\Controller\SchemaApiAbstract;
/**
 * @Title("foo")
 * @Description("lorem ipsum")
 * @PathParam(name="name", type="string", description="Name parameter", required=false, pattern="[A-z]+")
 * @PathParam(name="type", type="string", enum={"foo", "bar"})
 */
class Endpoint extends SchemaApiAbstract
{
    /**
     * @Description("Returns a collection")
     * @QueryParam(name="startIndex", type="integer", description="startIndex parameter", required=false)
     * @QueryParam(name="float", type="float")
     * @QueryParam(name="boolean", type="boolean")
     * @QueryParam(name="date", type="date")
     * @QueryParam(name="datetime", type="dateTime")
     * @Outgoing(code=200, schema="PSX\Generation\Complexae7d4b56")
     */
    public function doGet($record)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Complex70152cdf")
     * @Outgoing(code=201, schema="PSX\Generation\Complex31ead4d2")
     */
    public function doPost($record)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Complex774a7a4e")
     * @Outgoing(code=200, schema="PSX\Generation\Complex31ead4d2")
     */
    public function doPut($record)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Complex774a7a4e")
     * @Outgoing(code=200, schema="PSX\Generation\Complex31ead4d2")
     */
    public function doDelete($record)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Complex774a7a4e")
     * @Outgoing(code=200, schema="PSX\Generation\Complex31ead4d2")
     */
    public function doPatch($record)
    {
    }
}
PHP;

        $this->assertEquals(str_replace(array("\r\n", "\r"), "\n", $expect), $php, $php);
    }
}
