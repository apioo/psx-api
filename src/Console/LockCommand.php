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

use PSX\Api\Exception\BreakingChangesException;
use PSX\Api\Exception\LockException;
use PSX\Api\LockManager;
use PSX\Api\Scanner\FilterFactoryInterface;
use PSX\Api\ScannerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * LockCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class LockCommand extends Command
{
    public function __construct(private LockManager $lockManager, private ScannerInterface $scanner, private FilterFactoryInterface $filterFactory)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('api:lock')
            ->setDescription('Generates or verifies an API lock file, this protects')
            ->addArgument('goal', InputArgument::REQUIRED, 'Either "generate" or "verify"')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Optional a specific target lock file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filterName = $input->getOption('filter');
        if (empty($filterName)) {
            $filterName = $this->filterFactory->getDefault();
        }

        $file = $input->getOption('file');
        if (empty($file)) {
            $file = getcwd() . '/api.lock';
        }

        $filter = $this->filterFactory->getFilter($filterName);
        $spec = $this->scanner->generate($filter);

        try {
            $goal = $input->getArgument('goal');
            if ($goal === 'generate') {
                $this->lockManager->lock($spec, $file);
            } elseif ($goal === 'verify') {
                $this->lockManager->verify($spec, $file);
            } else {
                throw new LockException('Provided an invalid lock goal, must be either "generate" or "verify"');
            }
        } catch (BreakingChangesException $e) {
            $output->writeln('The API contains the following breaking changes, please revert these changes.');
            $output->writeln('To keep these changes you need to update the API lock file.');

            foreach ($e->getChanges() as $change) {
                $output->writeln('* ' . $change);
            }

            return self::FAILURE;
        } catch (LockException $e) {
            $output->writeln('Error: ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
