<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Console;

use PSX\Api\ApiManager;
use PSX\Api\GeneratorFactory;
use PSX\Api\GeneratorFactoryInterface;
use PSX\Schema\Generator\Code\Chunks;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ParseCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ParseCommand extends Command
{
    /**
     * @var \PSX\Api\ApiManager
     */
    protected $apiManager;

    /**
     * @var \PSX\Api\GeneratorFactoryInterface
     */
    protected $factory;

    public function __construct(ApiManager $apiManager, GeneratorFactoryInterface $factory)
    {
        parent::__construct();

        $this->apiManager = $apiManager;
        $this->factory    = $factory;
    }

    protected function configure()
    {
        $this
            ->setName('api:parse')
            ->setDescription('Parses an arbitrary source and outputs the schema in a specific format')
            ->addArgument('source', InputArgument::REQUIRED, 'The schema source this is either a absolute class name or schema file')
            ->addArgument('path', InputArgument::OPTIONAL, 'The path of the resource')
            ->addOption('dir', 'd', InputOption::VALUE_REQUIRED, 'The target directory')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Optional the output format possible values are: ' . implode(', ', GeneratorFactory::getPossibleTypes()))
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Optional a config value which gets passed to the generator');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $format = $input->getOption('format') ?? GeneratorFactoryInterface::CLIENT_PHP;
        if (!in_array($format, GeneratorFactory::getPossibleTypes())) {
            throw new \InvalidArgumentException('Provided an invalid format');
        }

        $dir = $input->getOption('dir') ?? getcwd();
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('Directory does not exist');
        }

        $config = $input->getOption('config');
        $specification = $this->apiManager->getApi($input->getArgument('source'), $input->getArgument('path'));

        $generator = $this->factory->getGenerator($format, $config);
        $extension = $this->factory->getFileExtension($format, $config);

        $output->writeln('Generating ...');

        $content = $generator->generate($specification);

        if ($content instanceof Chunks) {
            $content->writeTo($dir . '/sdk-' . $format .  '.zip');
        } else {
            file_put_contents($dir . '/output-' . $format . '.' . $extension, $content);
        }

        $output->writeln('Successful!');

        return 0;
    }
}
