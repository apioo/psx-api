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
use PSX\Api\Console\GenerateCommand;
use PSX\Api\GeneratorFactory;
use PSX\Api\GeneratorFactoryInterface;
use PSX\Api\Listing\MemoryListing;
use PSX\Api\Resource;
use PSX\Api\Tests\Parser\Annotation\TestController;
use PSX\Schema\SchemaManager;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * GenerateCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GenerateCommandTest extends TestCase
{
    public function testGenerateClientPhp()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::CLIENT_PHP,
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.php');
        $expect = file_get_contents(__DIR__ . '/resource/client_php.php');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateClientTypescript()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::CLIENT_TYPESCRIPT,
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.ts');
        $expect = file_get_contents(__DIR__ . '/resource/client_typescript.ts');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateMarkupHtml()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::MARKUP_HTML,
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.html');
        $expect = file_get_contents(__DIR__ . '/resource/markup_html.htm');

        $this->assertXmlStringNotEqualsXmlString('<div>' . $expect . '</div>', '<div>' . $actual . '</div>', $actual);
    }

    public function testGenerateMarkupMarkdown()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::MARKUP_MARKDOWN,
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.md');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/markup_markdown.md');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateMarkupTemplate()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::MARKUP_TEMPLATE,
            '--config' => 'markdown.md.twig',
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.md');
        $actual = str_replace(["\r\n", "\n", "\r"], "\n", $actual);
        $actual = preg_replace('/([0-9A-Fa-f]{32})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/markup_template.md');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateServerPhp()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::SERVER_PHP,
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.php');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/server_php.php');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateSpecJsonschema()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::SPEC_JSONSCHEMA,
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.json');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/spec_jsonschema.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGenerateSpecOpenAPI()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::SPEC_OPENAPI,
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.json');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/spec_openapi.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGenerateSpecRaml()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::SPEC_RAML,
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.raml');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/spec_raml.yaml');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateSpecSpecSwagger()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::SPEC_SWAGGER,
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.json');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/spec_swagger.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGenerateClientPhpRegexp()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--regexp' => '^/foo',
            '--format' => GeneratorFactoryInterface::CLIENT_PHP,
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.php');
        $expect = file_get_contents(__DIR__ . '/resource/client_php.php');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    protected function getGenerateCommand()
    {
        $schemaReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $schemaReader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $apiReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $apiReader->addNamespace('PSX\\Api\\Annotation');

        $apiManager = new ApiManager($apiReader, new SchemaManager($schemaReader));

        $listing = new MemoryListing();
        $listing->addResource($apiManager->getApi(TestController::class, '/foo'));

        $factory = new GeneratorFactory($schemaReader, 'urn:phpsx.org:2016#', 'http://foo.com', '');

        return new GenerateCommand($listing, $factory);
    }
}
