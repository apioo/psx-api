<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\GeneratorFactoryInterface;
use PSX\Api\Listing\FilterFactoryInterface;
use PSX\Api\ListingInterface;
use PSX\Schema\Generator\Code\Chunks;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GenerateCommand extends Command
{
    /**
     * @var \PSX\Api\ListingInterface
     */
    protected $listing;

    /**
     * @var \PSX\Api\GeneratorFactoryInterface
     */
    protected $factory;

    /**
     * @var \PSX\Api\Listing\FilterFactoryInterface
     */
    protected $filterFactory;

    /**
     * @param \PSX\Api\ListingInterface $listing
     * @param \PSX\Api\GeneratorFactoryInterface $factory
     * @param \PSX\Api\Listing\FilterFactoryInterface $filterFactory
     */
    public function __construct(ListingInterface $listing, GeneratorFactoryInterface $factory, FilterFactoryInterface $filterFactory = null)
    {
        parent::__construct();

        $this->listing = $listing;
        $this->factory = $factory;
        $this->filterFactory = $filterFactory;
    }

    protected function configure()
    {
        $this
            ->setName('api:generate')
            ->setDescription('Generates for each API resource a file in a specific format')
            ->addArgument('dir', InputArgument::OPTIONAL, 'The target directory')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Optional the output format possible values are: ' . implode(', ', GeneratorFactory::getPossibleTypes()))
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Optional a config value which gets passed to the generator')
            ->addOption('filter', 'i', InputOption::VALUE_REQUIRED, 'Optional a specific filter');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $format = $input->getOption('format') ?? GeneratorFactoryInterface::SPEC_OPENAPI;
        if (!in_array($format, GeneratorFactory::getPossibleTypes())) {
            throw new \InvalidArgumentException('Provided an invalid format');
        }

        $dir = $input->getArgument('dir') ?? getcwd();
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('Directory does not exist');
        }

        $config = $input->getOption('config');
        $filterName = $input->getOption('filter');

        if ($this->filterFactory instanceof FilterFactoryInterface && !empty($filterName)) {
            $filter = $this->filterFactory->getFilter($filterName);
        } else {
            $filter = null;
        }

        $generator = $this->factory->getGenerator($format, $config);
        $extension = $this->factory->getFileExtension($format, $config);

        $output->writeln('Generating ...');

        $content = $generator->generate($this->listing->findAll(null, $filter));

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
