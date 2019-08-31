<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Tests\Console;

use PHPUnit\Framework\TestCase;
use PSX\Api\ApiManager;
use PSX\Api\Console\ResourceCommand;
use PSX\Api\GeneratorFactory;
use PSX\Api\GeneratorFactoryInterface;
use PSX\Api\Listing\MemoryListing;
use PSX\Api\Resource;
use PSX\Api\Tests\Parser\Annotation\TestController;
use PSX\Schema\SchemaManager;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ResourceCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceCommandTest extends TestCase
{
    public function testGenerateClientPhp()
    {
        $command = $this->getResourceCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'path'     => '/foo',
            '--format' => GeneratorFactoryInterface::CLIENT_PHP,
        ));

        $actual = $commandTester->getDisplay();
        $expect = file_get_contents(__DIR__ . '/resource/client_php.php');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateClientTypescript()
    {
        $command = $this->getResourceCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'path'     => '/foo',
            '--format' => GeneratorFactoryInterface::CLIENT_TYPESCRIPT,
        ));

        $actual = $commandTester->getDisplay();
        $expect = file_get_contents(__DIR__ . '/resource/client_typescript.ts');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateMarkupHtml()
    {
        $command = $this->getResourceCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'path'     => '/foo',
            '--format' => GeneratorFactoryInterface::MARKUP_HTML,
        ));

        $actual = $commandTester->getDisplay();
        $expect = file_get_contents(__DIR__ . '/resource/markup_html.htm');

        $this->assertXmlStringNotEqualsXmlString('<div>' . $expect . '</div>', '<div>' . $actual . '</div>', $actual);
    }

    public function testGenerateMarkupMarkdown()
    {
        $command = $this->getResourceCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'path'     => '/foo',
            '--format' => GeneratorFactoryInterface::MARKUP_MARKDOWN,
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/markup_markdown.md');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateServerPhp()
    {
        $command = $this->getResourceCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'path'     => '/foo',
            '--format' => GeneratorFactoryInterface::SERVER_PHP,
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/server_php.php');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateSpecJsonschema()
    {
        $command = $this->getResourceCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'path'     => '/foo',
            '--format' => GeneratorFactoryInterface::SPEC_JSONSCHEMA,
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/spec_jsonschema.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGenerateSpecOpenAPI()
    {
        $command = $this->getResourceCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'path'     => '/foo',
            '--format' => GeneratorFactoryInterface::SPEC_OPENAPI,
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/spec_openapi.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGenerateSpecRaml()
    {
        $command = $this->getResourceCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'path'     => '/foo',
            '--format' => GeneratorFactoryInterface::SPEC_RAML,
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/spec_raml.yaml');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateSpecSwagger()
    {
        $command = $this->getResourceCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'path'     => '/foo',
            '--format' => GeneratorFactoryInterface::SPEC_SWAGGER,
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/spec_swagger.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    protected function getResourceCommand()
    {
        $schemaReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $schemaReader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $apiReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $apiReader->addNamespace('PSX\\Api\\Annotation');
        
        $apiManager = new ApiManager($apiReader, new SchemaManager($schemaReader));

        $listing = new MemoryListing();
        $listing->addResource($apiManager->getApi(TestController::class, '/foo'));

        $factory = new GeneratorFactory($schemaReader, 'urn:phpsx.org:2016#', 'http://foo.com', '');

        return new ResourceCommand($listing, $factory);
    }
}
