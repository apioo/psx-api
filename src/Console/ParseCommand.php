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

namespace PSX\Api\Console;

use PSX\Api\ApiManager;
use PSX\Api\GeneratorFactory;
use PSX\Api\GeneratorFactoryInterface;
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
 * @link    http://phpsx.org
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
            ->addArgument('path', InputArgument::REQUIRED, 'The path of the resource')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Optional the output format possible values are: ' . implode(', ', GeneratorFactory::getPossibleTypes()))
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Optional a config value which gets passed to the generator');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resource  = $this->apiManager->getApi($input->getArgument('source'), $input->getArgument('path'));
        $generator = $this->factory->getGenerator($input->getOption('format'), $input->getOption('config'));
        $response  = $generator->generate($resource);

        $output->write($response);
    }
}
