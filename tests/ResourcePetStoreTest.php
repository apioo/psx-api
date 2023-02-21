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

namespace PSX\Api\Tests;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Api\Generator;
use PSX\Api\Parser;
use PSX\Api\SpecificationInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * ResourceConversionTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ResourceConversionTest extends ApiManagerTestCase
{
    public function testClientPhp()
    {
        $specification = $this->getSpecification();
        $generator = new Generator\Client\Php('http://api.phpsx.org');

        $actual = (string) $generator->generate($specification->get('/pets'));
        $expect = file_get_contents(__DIR__ . '/Resource/petstore/client_php.php');
        $expect = str_replace(array("\r\n", "\r"), "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testClientTypescript()
    {
        $specification = $this->getSpecification();
        $generator = new Generator\Client\Typescript('http://api.phpsx.org');

        $actual = (string) $generator->generate($specification->get('/pets'));
        $expect = file_get_contents(__DIR__ . '/Resource/petstore/client_typescript.ts');
        $expect = str_replace(array("\r\n", "\r"), "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testMarkupHtml()
    {
        $specification = $this->getSpecification();
        $generator = new Generator\Markup\Html();

        $actual = $generator->generate($specification->get('/pets'));
        $expect = file_get_contents(__DIR__ . '/Resource/petstore/markup_html.htm');

        $this->assertXmlStringEqualsXmlString('<div>' . $expect . '</div>', '<div>' . $actual . '</div>', $actual);
    }

    public function testMarkupMarkdown()
    {
        $specification = $this->getSpecification();
        $generator = new Generator\Markup\Markdown();

        $actual = $generator->generate($specification->get('/pets'));

        $expect = file_get_contents(__DIR__ . '/Resource/petstore/markup_markdown.md');
        $expect = str_replace(array("\r\n", "\r"), "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testSpecOpenAPI()
    {
        $specification = $this->getSpecification();
        $generator = new Generator\Spec\OpenAPI(1, '/', 'urn:schema.phpsx.org#');

        $actual = $generator->generate($specification);
        $expect = file_get_contents(__DIR__ . '/Resource/petstore/spec_openapi.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testSpecRaml()
    {
        $specification = $this->getSpecification();
        $generator = new Generator\Spec\Raml(1, 'http://api.phpsx.org', 'urn:schema.phpsx.org#');

        $actual = $generator->generate($specification);
        $actual = json_encode(Yaml::parse($actual));

        $expect = file_get_contents(__DIR__ . '/Resource/petstore/spec_raml.yaml');
        $expect = json_encode(Yaml::parse($expect));

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    private function getSpecification(): SpecificationInterface
    {
        $parser = new Parser\OpenAPI(__DIR__ . '/Resource');

        return $parser->parse(json_encode(Yaml::parse($this->getOpenAPI())));
    }

    private function getOpenAPI()
    {
        return file_get_contents(__DIR__ . '/Resource/petstore.yaml');
    }
}
