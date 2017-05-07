<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Tests\Parser\Annotation;

/**
 * TestController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @Title("Test")
 * @Description("Test description")
 * @PathParam(name="fooId", type="string")
 */
class TestController
{
    public function __construct()
    {
    }

    public function getDocumentation($version = null)
    {
        return null;
    }

    /**
     * @Description("!include description.md")
     * @QueryParam(name="foo", type="string", description="Test")
     * @QueryParam(name="bar", type="string", required=true)
     * @QueryParam(name="baz", type="string", enum={"foo", "bar"})
     * @QueryParam(name="boz", type="string", pattern="[A-z]+")
     * @QueryParam(name="integer", type="integer")
     * @QueryParam(name="number", type="number")
     * @QueryParam(name="date", type="date")
     * @QueryParam(name="boolean", type="boolean")
     * @QueryParam(name="string", type="string")
     * @Incoming(schema="../schema/schema.json")
     * @Outgoing(code=200, schema="../schema/schema.json")
     */
    protected function doGet()
    {
    }
}
