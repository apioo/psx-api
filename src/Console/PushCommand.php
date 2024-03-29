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

use Composer\InstalledVersions;
use PSX\Api\Generator\Spec\TypeAPI;
use PSX\Api\Scanner\FilterFactoryInterface;
use PSX\Api\ScannerInterface;
use PSX\Http\Client\Client;
use PSX\Http\Client\ClientInterface;
use PSX\Http\Client\GetRequest;
use PSX\Http\Client\PostRequest;
use Symfony\Component\Console\Command\Command;
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
    private const TYPEHUB_URL = 'https://api.typehub.cloud';

    private ScannerInterface $scanner;
    private FilterFactoryInterface $filterFactory;
    private ClientInterface $client;

    public function __construct(ScannerInterface $scanner, FilterFactoryInterface $filterFactory)
    {
        parent::__construct();

        $this->scanner = $scanner;
        $this->filterFactory = $filterFactory;
        $this->client = new Client();
    }

    protected function configure(): void
    {
        $this
            ->setName('api:push')
            ->setDescription('Submits the specification to the typehub.cloud platform')
            ->addArgument('name', InputArgument::REQUIRED, 'The target document name')
            ->addOption('client_id', 'u', InputOption::VALUE_REQUIRED, 'Optional the client id')
            ->addOption('client_secret', 's', InputOption::VALUE_REQUIRED, 'Optional the client secret')
            ->addOption('filter', 'i', InputOption::VALUE_REQUIRED, 'Optional a specific filter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $clientId = $input->getOption('client_id');
        $clientSecret = $input->getOption('client_secret');

        $filterName = $input->getOption('filter');
        if (empty($filterName)) {
            $filterName = $this->filterFactory->getDefault();
        }

        $filter = $this->filterFactory->getFilter($filterName);
        $spec   = (string) (new TypeAPI())->generate($this->scanner->generate($filter));
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

        $accessToken = $this->obtainAccessToken($clientId, $clientSecret);
        $userName = $this->obtainUserName($accessToken);

        $this->importDocument($accessToken, $userName, $name, $spec);

        $output->writeln('Document import Successful!');

        return Command::SUCCESS;
    }

    private function importDocument(string $accessToken, string $user, string $document, string $spec): void
    {
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'User-Agent' => 'PSX API ' . InstalledVersions::getVersion('psx/api'),
        ];

        $request  = new GetRequest(self::TYPEHUB_URL . '/document/' . $user . '/' . $document, $headers);
        $response = $this->client->request($request);

        if ($response->getStatusCode() >= 500) {
            throw new \RuntimeException('The server returned an error code: ' . $response->getStatusCode());
        } elseif ($response->getStatusCode() === 404) {
            throw new \RuntimeException('The provided document does not exist, please create the document at typehub.cloud in order to use it');
        } elseif ($response->getStatusCode() === 200) {
            // the document already exists
        } else {
            throw new \RuntimeException('The server returned an invalid status code: ' . $response->getStatusCode());
        }

        $request  = new PostRequest(self::TYPEHUB_URL . '/document/' . $user . '/' . $document . '/import', $headers, $spec);
        $response = $this->client->request($request);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Could not import document, the server returned a wrong status code: ' . $response->getStatusCode() . ' - ' . $response->getBody());
        }

        $data = \json_decode((string) $response->getBody());
        if (!$data instanceof \stdClass) {
            throw new \RuntimeException('Could not import document, the server returned invalid JSON data');
        }

        $success = $data->success ?? false;
        if ($success === false) {
            throw new \RuntimeException('Could not import document, the server returned a wrong response: ' . \json_encode($data, \JSON_PRETTY_PRINT));
        }
    }

    private function obtainAccessToken(string $user, string $password): string
    {
        $headers = [
            'Authorization' => 'Basic ' . base64_encode($user . ':' . $password),
            'Content-Type' => 'application/x-www-form-urlencoded',
            'User-Agent' => 'PSX API ' . InstalledVersions::getVersion('psx/api'),
        ];

        $request = new PostRequest(self::TYPEHUB_URL . '/authorization/token', $headers, http_build_query([
            'grant_type' => 'client_credentials'
        ]));

        $response = $this->client->request($request);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Could not obtain access token, the server returned an invalid status code: ' . $response->getStatusCode() . ', please register at typehub.cloud to obtain a fitting client id and secret');
        }

        $data = \json_decode((string) $response->getBody());
        if (!$data instanceof \stdClass) {
            throw new \RuntimeException('Could not obtain access token, the server returned invalid JSON data');
        }

        $accessToken = $data->access_token ?? '';
        if (empty($accessToken) || !is_string($accessToken)) {
            throw new \RuntimeException('Could not obtain access token');
        }

        return $accessToken;
    }

    private function obtainUserName(string $accessToken): string
    {
        $request = new GetRequest(self::TYPEHUB_URL . '/authorization/whoami', [
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
            'User-Agent' => 'PSX API ' . InstalledVersions::getVersion('psx/api'),
        ]);

        $response = $this->client->request($request);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Could not obtain user info: ' . $response->getStatusCode());
        }

        $data = \json_decode((string) $response->getBody());
        if (!$data instanceof \stdClass) {
            throw new \RuntimeException('Could not obtain user info, the server returned invalid JSON data');
        }

        $userName = $data->name ?? null;
        if (empty($userName) || !is_string($userName)) {
            throw new \RuntimeException('Could not obtain user info, the server returned no user name');
        }

        return $userName;
    }
}
