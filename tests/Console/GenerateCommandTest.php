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
            '--config' => 'Acme\\Sdk\\Generated',
        ));

        $this->assertFileExists(__DIR__ . '/output/FooResource.php');
        $this->assertFileExists(__DIR__ . '/output/FooResourceSchema.php');
        $this->assertFileExists(__DIR__ . '/output/GetQuery.php');
        $this->assertFileExists(__DIR__ . '/output/Rating.php');
        $this->assertFileExists(__DIR__ . '/output/Song.php');
    }

    public function testGenerateClientTypescript()
    {
        $command = $this->getGenerateCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'dir'      => __DIR__ . '/output',
            '--format' => GeneratorFactoryInterface::CLIENT_TYPESCRIPT,
            '--config' => 'Acme.Sdk.Generated',
        ));

        $this->assertFileExists(__DIR__ . '/output/FooResource.ts');
        $this->assertFileExists(__DIR__ . '/output/FooResourceSchema.ts');
        $this->assertFileExists(__DIR__ . '/output/GetQuery.ts');
        $this->assertFileExists(__DIR__ . '/output/Rating.ts');
        $this->assertFileExists(__DIR__ . '/output/Song.ts');
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
