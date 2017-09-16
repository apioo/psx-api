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
use PSX\Api\Console\ApiCommand;
use PSX\Api\Resource;
use PSX\Api\Tests\Parser\Annotation\TestController;
use PSX\Schema\SchemaManager;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ApiCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateHtml()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'html',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $expect = file_get_contents(__DIR__ . '/html.htm');

        $this->assertXmlStringNotEqualsXmlString('<div>' . $expect . '</div>', '<div>' . $actual . '</div>', $actual);
    }

    public function testGenerateJsonschema()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'jsonschema',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/jsonschema.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }
    
    public function testGenerateMarkdown()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'markdown',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/markdown.md');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateOpenAPI()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'openapi',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/openapi.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGeneratePhp()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'php',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/php.php');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateRaml()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'raml',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/raml.yaml');
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateSerialize()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'serialize',
            'path'   => '/foo',
        ));

        $actual   = $commandTester->getDisplay();
        $resource = unserialize($actual);

        $this->assertInstanceOf(Resource::class, $resource);
    }

    public function testGenerateSwagger()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'swagger',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $expect = file_get_contents(__DIR__ . '/swagger.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    protected function getApiCommand()
    {
        $schemaReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $schemaReader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $apiReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $apiReader->addNamespace('PSX\\Api\\Annotation');

        return new ApiCommand(
            new ApiManager(
                $apiReader, 
                new SchemaManager($schemaReader)
            ),
            $schemaReader, 
            'urn:phpsx.org:2016#', 
            'http://foo.com', 
            ''
        );
    }
}
