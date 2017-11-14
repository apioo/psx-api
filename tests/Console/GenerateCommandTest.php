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

namespace PSX\Api\Tests\Console;

use PSX\Api\ApiManager;
use PSX\Api\Console\GenerateCommand;
use PSX\Api\GeneratorFactory;
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
class GenerateCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateHtml()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => 'html',
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.html');
        $expect = file_get_contents(__DIR__ . '/resource/html.htm');

        $this->assertXmlStringNotEqualsXmlString('<div>' . $expect . '</div>', '<div>' . $actual . '</div>', $actual);
    }

    public function testGenerateJsonschema()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => 'jsonschema',
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.json');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/jsonschema.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }
    
    public function testGenerateMarkdown()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => 'markdown',
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.md');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/markdown.md');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateOpenAPI()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => 'openapi',
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.json');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/openapi.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGeneratePhp()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => 'php',
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.php');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/php.php');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateRaml()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => 'raml',
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.raml');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/raml.yaml');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateSerialize()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => 'serialize',
        ));

        $actual   = file_get_contents(__DIR__ . '/output/foo.ser');
        $resource = unserialize($actual);

        $this->assertInstanceOf(Resource::class, $resource);
    }

    public function testGenerateSwagger()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => 'swagger',
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.json');
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/swagger.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGenerateTemplate()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => 'template',
            '--config' => 'markdown.md.twig',
        ));

        $actual = file_get_contents(__DIR__ . '/output/foo.md');
        $actual = str_replace(["\r\n", "\n", "\r"], "\n", $actual);
        $actual = preg_replace('/([0-9A-Fa-f]{32})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/resource/template.md');
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
