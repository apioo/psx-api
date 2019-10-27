<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2019 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Schema\Generator\Code\Chunks;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ResourceCommand
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceCommand extends Command
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
            ->setName('api:resource')
            ->setDescription('Outputs an API resource in a specific format')
            ->addArgument('path', InputArgument::REQUIRED, 'The path of the resource')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Optional the output format possible values are: ' . implode(', ', GeneratorFactory::getPossibleTypes()))
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Optional a config value which gets passed to the generator')
            ->addOption('version', 'v', InputOption::VALUE_REQUIRED, 'Optional the version of the resource');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resource  = $this->listing->getResource($input->getArgument('path'), $input->getOption('version'));
        $generator = $this->factory->getGenerator($input->getOption('format'), $input->getOption('config'));
        $response  = $generator->generate($resource);

        if ($response instanceof Chunks) {
            // we need to simply write data to the stdout and can write files. In
            // case the generator returns a chunked response we simply format the
            // response as HTTP multipart
            $mime = $this->factory->getMime($input->getOption('format'), $input->getOption('config'));
            $boundary = '85d62adb6dd029cc080b15eb2086a8e054887f8a';
            foreach ($response->getChunks() as $identifier => $code) {
                $output->writeln('--' . $boundary);
                $output->writeln('Content-Type: ' . $mime);
                $output->writeln('Content-Disposition: attachment; filename="' . $identifier . '"');
                $output->writeln('Content-Length: ' . strlen($code));
                $output->writeln('');
                $output->write($code);
                $output->writeln('');
            }
            $output->writeln('--' . $boundary . '--');
        } else {
            $output->write($response);
        }
    }
}
