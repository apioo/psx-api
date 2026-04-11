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

namespace PSX\Api\Console;

use PSX\Api\Exception\PublishException;
use PSX\Api\TypeHub\Publisher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * PushCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class PushCommand extends Command
{
    public function __construct(private readonly Publisher $publisher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('api:push')
            ->setDescription('Submits the specification to the typehub.cloud platform')
            ->addArgument('name', InputArgument::REQUIRED, 'The target document name')
            ->addOption('client_id', 'u', InputOption::VALUE_REQUIRED, 'Optional the client id')
            ->addOption('client_secret', 's', InputOption::VALUE_REQUIRED, 'Optional the client secret')
            ->addOption('filter', 'i', InputOption::VALUE_REQUIRED, 'Optional a specific filter')
            ->addOption('standalone', 'a', InputOption::VALUE_NONE, 'Ignore base url and security settings');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $clientId = $input->getOption('client_id');
        $clientSecret = $input->getOption('client_secret');
        $filterName = $input->getOption('filter');
        $standalone = (bool) $input->getOption('standalone');

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        if (empty($clientId)) {
            $question = new Question('Client-Id: ');
            $clientId = $helper->ask($input, $output, $question);
        }

        if (empty($clientSecret)) {
            $question = new Question('Client-Secret: ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $clientSecret = $helper->ask($input, $output, $question);
        }

        try {
            $this->publisher->publish($name, $clientId, $clientSecret, $filterName, $standalone);

            $output->writeln('Document import Successful!');

            return Command::SUCCESS;
        } catch (PublishException $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }
    }
}
