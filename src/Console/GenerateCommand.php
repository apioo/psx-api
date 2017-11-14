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

use PSX\Api\GeneratorFactory;
use PSX\Api\GeneratorFactoryInterface;
use PSX\Api\ListingInterface;
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

    public function __construct(ListingInterface $listing, GeneratorFactoryInterface $factory)
    {
        parent::__construct();

        $this->listing = $listing;
        $this->factory = $factory;
    }

    protected function configure()
    {
        $this
            ->setName('api:generate')
            ->setDescription('Generates for each API resource a file in a specific format')
            ->addArgument('dir', InputArgument::REQUIRED, 'The target directory')
            ->addArgument('filter', InputArgument::OPTIONAL, 'Optional a path regexp filter')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Optional the output format possible values are: ' . implode(', ', GeneratorFactory::getPossibleTypes()))
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Optional a config value which gets passed to the generator');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resources = $this->listing->getResourceIndex();
        $progress  = new ProgressBar($output, count($resources));

        $dir = $input->getArgument('dir');
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('Directory does not exist');
        }

        $filter = $input->getArgument('filter');

        $generator = $this->factory->getGenerator($input->getOption('format'), $input->getOption('config'));
        $extension = $this->factory->getFileExtension($input->getOption('format'), $input->getOption('config'));

        $progress->start();

        foreach ($resources as $resource) {
            if (!empty($filter) && !preg_match('/^' . $filter . '$/', $resource->getPath())) {
                continue;
            }

            $progress->setMessage('Generating ' . $resource->getPath());

            $content  = $generator->generate($this->listing->getResource($resource->getPath()));
            $file     = $dir . '/' . $this->getFileName($resource->getPath(), $extension);

            file_put_contents($file, $content);

            $progress->advance();
        }

        $progress->finish();

        $output->writeln('Successful!');
    }

    private function getFileName($path, $extension)
    {
        $path = trim($path, '/');
        $path = preg_replace('/[^A-Za-z0-9]/', '_', $path);

        return $path . '.' . $extension;
    }
}
