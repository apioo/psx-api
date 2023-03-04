<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\Scanner\FilterFactoryInterface;
use PSX\Api\ScannerInterface;
use PSX\Api\SpecificationInterface;
use PSX\Http\Client\Client;
use PSX\Http\Client\ClientInterface;
use PSX\Http\Client\GetRequest;
use PSX\Http\Client\PostRequest;
use PSX\Http\Client\PutRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
    private ScannerInterface $scanner;
    private ?FilterFactoryInterface $filterFactory;
    private ClientInterface $client;

    public function __construct(ScannerInterface $scanner, ?FilterFactoryInterface $filterFactory = null)
    {
        parent::__construct();

        $this->scanner = $scanner;
        $this->filterFactory = $filterFactory;
        $this->client = new Client();
    }

    protected function configure()
    {
        $this
            ->setName('api:push')
            ->setDescription('Submits the specification to the typehub.cloud platform')
            ->addOption('filter', 'i', InputOption::VALUE_REQUIRED, 'Optional a specific filter');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filterName = $input->getOption('filter');
        if ($this->filterFactory instanceof FilterFactoryInterface && !empty($filterName)) {
            $filter = $this->filterFactory->getFilter($filterName);
        } else {
            $filter = null;
        }

        $helper = $this->getHelper('question');
        $specification = $this->scanner->generate($filter);

        $question = new Question('Please enter your typehub.cloud username');
        $user = $helper->ask($input, $output, $question);

        $question = new Question('Password');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $question);

        $question = new Question('Document name');
        $document = $helper->ask($input, $output, $question);

        $question = new ConfirmationQuestion('Do you really want to import the document "' . $document . '"?', false);
        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $this->importDocument($user, $password, $document, $specification);

        $output->writeln('Document import Successful!');

        return Command::SUCCESS;
    }

    private function importDocument(string $user, string $password, string $document, SpecificationInterface $specification)
    {
        $accessToken = $this->obtainAccessToken($user, $password);
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ];

        $request  = new GetRequest('https://api.typehub.cloud/document/' . $user . '/' . $document, $headers);
        $response = $this->client->request($request);

        if ($response->getStatusCode() >= 500) {
            throw new \RuntimeException('The server returned an error code: ' . $response->getStatusCode());
        } elseif ($response->getStatusCode() === 404) {
            $this->createDocument($user, $document, $headers);
        } elseif ($response->getStatusCode() === 200) {
            // the document already exists
        } else {
            throw new \RuntimeException('The server returned an invalid status code: ' . $response->getStatusCode());
        }

        $request  = new PutRequest('https://api.typehub.cloud/document/' . $user . '/' . $document . '/import', $headers, \json_encode($specification));
        $response = $this->client->request($request);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Could not import document, the server returned a wrong status code: ' . $response->getStatusCode());
        }

        $success = $data->success ?? false;
        if ($success) {
            throw new \RuntimeException('Could not import document, the server returned a wrong response: ' . \json_encode($data, \JSON_PRETTY_PRINT));
        }
    }

    private function obtainAccessToken(string $user, string $password): string
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($user . ':' . $password),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $request = new PostRequest('https://api.typehub.cloud/authorization/token', $headers, http_build_query([
            'grant_type' => 'client_credentials'
        ]));

        $response = $this->client->request($request);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Could not obtain access token, the server returned an invalid status code: ' . $response->getStatusCode());
        }

        $data = \json_encode((string) $response->getBody());
        if (!$data instanceof \stdClass) {
            throw new \RuntimeException('Could not obtain access token, the server returned invalid JSON data');
        }

        $accessToken = $data->access_token ?? '';
        if (empty($acccessToken)) {
            throw new \RuntimeException('Could not obtain access token');
        }

        return $accessToken;
    }

    private function createDocument(string $user, string $document, array $headers): void
    {
        $request = new PostRequest('https://api.typehub.cloud/document/' . $user, $headers, \json_encode([
            'name' => $document,
            'description' => '',
        ]));

        $response = $this->client->request($request);
        if ($response->getStatusCode() !== 201) {
            throw new \RuntimeException('Could not create document, the server returned an invalid status code: ' . $response->getStatusCode());
        }

        $data = \json_encode((string) $response->getBody());
        if (!$data instanceof \stdClass) {
            throw new \RuntimeException('Could not create document, the server returned invalid JSON data');
        }

        $success = $data->success ?? false;
        if ($success) {
            throw new \RuntimeException('Could not create document, the server returned a wrong response: ' . \json_encode($data, \JSON_PRETTY_PRINT));
        }
    }
}
