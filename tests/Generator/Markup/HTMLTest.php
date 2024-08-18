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

namespace PSX\Api\Tests\Generator\Markup;

use PSX\Api\Generator\Markup\HTML;
use PSX\Api\Tests\Generator\GeneratorTestCase;

/**
 * HTMLTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class HTMLTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new HTML();

        $actual = $generator->generate($this->getSpecification());
        $actual = preg_replace('/psx_model_Object([0-9A-Fa-f]{8})/', '[dynamic_id]', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/html.htm');

        $this->assertXmlStringEqualsXmlString('<div>' . $expect . '</div>', '<div>' . $actual . '</div>', $actual);
    }
}
