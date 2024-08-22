<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\Parser\Attribute\OperationIdBuilder;
use PSX\Api\Repository\LocalRepository;
use PSX\Api\Tests\Parser\Attribute\TestController;
use PSX\Schema\SchemaManager;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ParseCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ParseCommandTest extends TestCase
{
    public function testGenerateSpecOpenAPI()
    {
        $command = $this->getParseCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'type'   => LocalRepository::SPEC_OPENAPI,
            'dir'    => __DIR__ . '/output',
        ));

        $actual = file_get_contents(__DIR__ . '/output/output-spec-openapi.json');
        $expect = file_get_contents(__DIR__ . '/resource/spec_openapi.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    protected function getParseCommand(): ParseCommand
    {
        $apiManager = new ApiManager(new SchemaManager(), new OperationIdBuilder(new ArrayAdapter(), false));
        $factory    = GeneratorFactory::fromLocal('http://foo.com/');

        return new ParseCommand($apiManager, $factory);
    }
}
