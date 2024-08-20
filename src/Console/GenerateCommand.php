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

use PSX\Api\GeneratorFactory;
use PSX\Api\Scanner\FilterFactoryInterface;
use PSX\Api\ScannerInterface;
use PSX\Schema\Generator\Code\Chunks;
use PSX\Schema\Generator\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GenerateCommand extends Command
{
    private ScannerInterface $scanner;
    private GeneratorFactory $factory;
    private ?FilterFactoryInterface $filterFactory;

    public function __construct(ScannerInterface $scanner, GeneratorFactory $factory, ?FilterFactoryInterface $filterFactory = null)
    {
        parent::__construct();

        $this->scanner = $scanner;
        $this->factory = $factory;
        $this->filterFactory = $filterFactory;
    }

    protected function configure(): void
    {
        $this
            ->setName('api:generate')
            ->setDescription('Generates for each API resource a file in a specific format')
            ->addArgument('type', InputArgument::REQUIRED, 'The generator type')
            ->addArgument('dir', InputArgument::OPTIONAL, 'The target directory')
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Optional a config value which gets passed to the generator')
            ->addOption('filter', 'i', InputOption::VALUE_REQUIRED, 'Optional a specific filter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $registry = $this->factory->factory();

        $format = $input->getArgument('type');
        if (!in_array($format, $registry->getPossibleTypes())) {
            throw new \InvalidArgumentException('Provided an invalid format, possible types are: ' . implode(', ', $registry->getPossibleTypes()));
        }

        $dir = $input->getArgument('dir') ?? getcwd();
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('Directory does not exist');
        }

        $config = Config::fromQueryString($input->getOption('config'));
        $filterName = $input->getOption('filter');

        if ($this->filterFactory instanceof FilterFactoryInterface && !empty($filterName)) {
            $filter = $this->filterFactory->getFilter($filterName);
        } else {
            $filter = null;
        }

        $generator = $registry->getGenerator($format, $config);
        $extension = $registry->getFileExtension($format);

        $output->writeln('Generating ...');

        $content = $generator->generate($this->scanner->generate($filter));

        if ($content instanceof Chunks) {
            if (!empty($filterName)) {
                $file = 'sdk-' . $format .  '-' . $filterName . '.zip';
            } else {
                $file = 'sdk-' . $format .  '.zip';
            }

            $content->writeTo($dir . '/' . $file);
        } else {
            if (!empty($filterName)) {
                $file = 'output-' . $format . '-' . $filterName . '.' . $extension;
            } else {
                $file = 'output-' . $format . '.' . $extension;
            }

            file_put_contents($dir . '/' . $file, $content);
        }

        $output->writeln('Successful!');

        return 0;
    }
}
