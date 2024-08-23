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
use PSX\Api\Console\GenerateCommand;
use PSX\Api\GeneratorFactory;
use PSX\Api\Parser\Attribute\Builder;
use PSX\Api\Repository\LocalRepository;
use PSX\Api\Scanner\Memory;
use PSX\Api\Tests\Parser\Attribute\TestController;
use PSX\Schema\SchemaManager;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * GenerateCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GenerateCommandTest extends TestCase
{
    public function testGenerateClientPhp()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'type'     => LocalRepository::CLIENT_PHP,
            'dir'      => __DIR__ . '/output',
            '--config' => 'Acme\\Sdk\\Generated',
        ));

        $this->assertFileExists(__DIR__ . '/output/sdk-client-php.zip');
    }

    public function testGenerateSpecOpenAPI()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'type'     => LocalRepository::SPEC_OPENAPI,
            'dir'      => __DIR__ . '/output',
        ));

        $this->assertFileExists(__DIR__ . '/output/output-spec-openapi.json');
    }

    protected function getGenerateCommand()
    {
        $apiManager = new ApiManager(new SchemaManager(), new Builder(new ArrayAdapter(), false));

        $scanner = new Memory();
        $scanner->merge($apiManager->getApi(TestController::class));

        $factory = GeneratorFactory::fromLocal();

        return new GenerateCommand($scanner, $factory);
    }
}
