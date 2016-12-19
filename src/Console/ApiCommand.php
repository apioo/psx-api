<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

use Doctrine\Common\Annotations\Reader;
use PSX\Api\ApiManager;
use PSX\Api\Generator;
use PSX\Http\Client;
use PSX\Schema\Parser;
use PSX\Schema\SchemaManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ApiCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiCommand extends Command
{
    /**
     * @var \PSX\Api\ApiManager
     */
    protected $apiManager;

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $dispatch;

    public function __construct(ApiManager $apiManager, Reader $reader, $namespace, $url, $dispatch)
    {
        parent::__construct();

        $this->apiManager = $apiManager;
        $this->reader     = $reader;
        $this->namespace  = $namespace;
        $this->url        = $url;
        $this->dispatch   = $dispatch;
    }

    protected function configure()
    {
        $this
            ->setName('api')
            ->setDescription('Parses an arbitrary source and outputs the schema in a specific format')
            ->addArgument('source', InputArgument::REQUIRED, 'The schema source this is either a absolute class name or schema file')
            ->addArgument('format', InputArgument::OPTIONAL, 'Optional the output format possible values are: swagger, raml, php, serialize, jsonschema')
            ->addArgument('path', InputArgument::OPTIONAL, 'Optional the path of the resource');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->apiManager->getApi($input->getArgument('source'), $input->getArgument('path'));

        $targetNamespace = $this->namespace;
        $baseUri         = $this->url . '/' . $this->dispatch;
        $basePath        = '/' . $this->dispatch;

        switch ($input->getArgument('format')) {
            case 'php':
                $generator = new Generator\Php();
                $response  = $generator->generate($api);
                break;

            case 'raml':
                $generator = new Generator\Raml('psx', 1, $baseUri, $targetNamespace);
                $response  = $generator->generate($api);
                break;

            case 'serialize':
                $response = serialize($api);
                break;

            case 'jsonschema':
                $generator = new Generator\JsonSchema($targetNamespace);
                $response  = $generator->generate($api);
                break;

            default:
            case 'swagger':
                $generator = new Generator\Swagger($this->reader, 1, $basePath, $targetNamespace);
                $response  = $generator->generate($api);
                break;
        }

        $output->write($response);
    }
}
