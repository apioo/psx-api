<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2019 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\Console\ParseCommand;
use PSX\Api\GeneratorFactory;
use PSX\Api\GeneratorFactoryInterface;
use PSX\Api\Tests\Parser\Annotation\TestController;
use PSX\Schema\SchemaManager;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ParseCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ParseCommandTest extends TestCase
{
    public function testGenerateSpecOpenAPI()
    {
        $command = $this->getParseCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source'   => TestController::class,
            'path'     => '/foo',
            '--format' => GeneratorFactoryInterface::SPEC_OPENAPI,
        ));

        $actual = $commandTester->getDisplay();
        $expect = file_get_contents(__DIR__ . '/resource/spec_openapi.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    protected function getParseCommand()
    {
        $schemaReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $schemaReader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $apiReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $apiReader->addNamespace('PSX\\Api\\Annotation');

        $apiManager = new ApiManager($apiReader, new SchemaManager($schemaReader));
        $factory    = new GeneratorFactory($schemaReader, 'urn:phpsx.org:2016#', 'http://foo.com', '');

        return new ParseCommand($apiManager, $factory);
    }
}
