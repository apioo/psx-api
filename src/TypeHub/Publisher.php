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

namespace PSX\Api\TypeHub;

use Composer\InstalledVersions;
use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\Exception\GeneratorException;
use PSX\Api\Exception\PublishException;
use PSX\Api\Generator\ConfigurationAwareInterface;
use PSX\Api\GeneratorFactory;
use PSX\Api\Repository\LocalRepository;
use PSX\Api\Scanner\FilterFactoryInterface;
use PSX\Api\ScannerInterface;
use PSX\Api\Specification;
use PSX\Http\Client\ClientInterface;
use PSX\Http\Client\GetRequest;
use PSX\Http\Client\PostRequest;
use stdClass;
use function json_decode;
use function json_encode;

/**
 * Publisher
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Publisher implements PublisherInterface
{
    private const TYPEHUB_URL = 'https://api.typehub.cloud';
    private const TOKEN_CACHE_KEY = 'psx-typehub-token';

    public function __construct(
        private readonly ScannerInterface $scanner,
        private readonly GeneratorFactory $factory,
        private readonly FilterFactoryInterface $filterFactory,
        private readonly ClientInterface $client,
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    public function get(?string $filterName = null, bool $standalone = false): string
    {
        if (empty($filterName)) {
            $filterName = $this->filterFactory->getDefault();
        }

        $registry = $this->factory->factory();
        $generator = $registry->getGenerator(LocalRepository::SPEC_TYPEAPI);

        $filter = $this->filterFactory->getFilter($filterName);
        $spec = $this->scanner->generate($filter);

        if ($standalone && $spec instanceof Specification) {
            $spec->setBaseUrl(null);
            $spec->setSecurity(null);

            if ($generator instanceof ConfigurationAwareInterface) {
                $generator->setBaseUrl(null);
                $generator->setSecurity(null);
            }
        }

        return (string) $generator->generate($spec);
    }

    public function publish(string $name, string $clientId, string $clientSecret, ?string $filterName = null, bool $standalone = false): void
    {
        try {
            $result = $this->get($filterName, $standalone);
        } catch (GeneratorException $e) {
            throw new PublishException('Could not generate specification, got: ' . $e->getMessage(), previous: $e);
        }

        $accessToken = $this->obtainAccessToken($clientId, $clientSecret);
        $userName = $this->obtainUserName($accessToken);

        $this->importDocument($accessToken, $userName, $name, $result);
    }

    public function changelog(string $name, string $clientId, string $clientSecret): Changelog
    {
        $accessToken = $this->obtainAccessToken($clientId, $clientSecret);
        $userName = $this->obtainUserName($accessToken);

        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'User-Agent' => 'PSX API ' . InstalledVersions::getVersion('psx/api'),
        ];

        $request  = new PostRequest(self::TYPEHUB_URL . '/document/' . $userName . '/' . $name . '/changelog', $headers);
        $response = $this->client->request($request);

        if ($response->getStatusCode() !== 200) {
            throw new PublishException('Could not obtain changelog, the server returned a wrong status code: ' . $response->getStatusCode() . ' - ' . $response->getBody());
        }

        $data = json_decode((string) $response->getBody());
        if (!$data instanceof stdClass) {
            throw new PublishException('Could not obtain changelog, the server returned invalid JSON data');
        }

        return Changelog::from($data);
    }

    public function tag(string $name, string $clientId, string $clientSecret): Tag
    {
        $accessToken = $this->obtainAccessToken($clientId, $clientSecret);
        $userName = $this->obtainUserName($accessToken);

        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'User-Agent' => 'PSX API ' . InstalledVersions::getVersion('psx/api'),
        ];

        $request  = new PostRequest(self::TYPEHUB_URL . '/document/' . $userName . '/' . $name . '/tag', $headers);
        $response = $this->client->request($request);

        $successful = $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        if (!$successful) {
            throw new PublishException('Could not create tag, the server returned a wrong status code: ' . $response->getStatusCode() . ' - ' . $response->getBody());
        }

        $data = json_decode((string) $response->getBody());
        if (!$data instanceof stdClass) {
            throw new PublishException('Could not create tag, the server returned invalid JSON data');
        }

        $success = $data->success ?? false;
        if ($success === false) {
            throw new PublishException('Could not create tag, the server returned a wrong response: ' . json_encode($data, \JSON_PRETTY_PRINT));
        }

        return Tag::from($data);
    }

    /**
     * @throws PublishException
     */
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
            throw new PublishException('The server returned an error code: ' . $response->getStatusCode());
        } elseif ($response->getStatusCode() === 404) {
            throw new PublishException('The provided document does not exist, please create the document at typehub.cloud in order to use it');
        } elseif ($response->getStatusCode() === 200) {
            // the document already exists
        } else {
            throw new PublishException('The server returned an invalid status code: ' . $response->getStatusCode());
        }

        $request  = new PostRequest(self::TYPEHUB_URL . '/document/' . $user . '/' . $document . '/import', $headers, $spec);
        $response = $this->client->request($request);

        if ($response->getStatusCode() !== 200) {
            throw new PublishException('Could not import document, the server returned a wrong status code: ' . $response->getStatusCode() . ' - ' . $response->getBody());
        }

        $data = json_decode((string) $response->getBody());
        if (!$data instanceof stdClass) {
            throw new PublishException('Could not import document, the server returned invalid JSON data');
        }

        $success = $data->success ?? false;
        if ($success === false) {
            throw new PublishException('Could not import document, the server returned a wrong response: ' . json_encode($data, \JSON_PRETTY_PRINT));
        }
    }

    /**
     * @throws PublishException
     */
    private function obtainAccessToken(string $user, string $password): string
    {
        $item = $this->cache->getItem(self::TOKEN_CACHE_KEY);
        if ($item->isHit()) {
            return $item->get();
        }

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
            throw new PublishException('Could not obtain access token, the server returned an invalid status code: ' . $response->getStatusCode() . ', please register at typehub.cloud to obtain a fitting client id and secret');
        }

        $data = json_decode((string) $response->getBody());
        if (!$data instanceof stdClass) {
            throw new PublishException('Could not obtain access token, the server returned invalid JSON data');
        }

        $accessToken = $data->access_token ?? '';
        if (empty($accessToken) || !is_string($accessToken)) {
            throw new PublishException('Could not obtain access token');
        }

        $item->expiresAfter($data->expires_in ?? 3600);
        $item->set($accessToken);
        $this->cache->save($item);

        return $accessToken;
    }

    /**
     * @throws PublishException
     */
    private function obtainUserName(string $accessToken): string
    {
        $request = new GetRequest(self::TYPEHUB_URL . '/authorization/whoami', [
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
            'User-Agent' => 'PSX API ' . InstalledVersions::getVersion('psx/api'),
        ]);

        $response = $this->client->request($request);
        if ($response->getStatusCode() !== 200) {
            throw new PublishException('Could not obtain user info: ' . $response->getStatusCode());
        }

        $data = json_decode((string) $response->getBody());
        if (!$data instanceof stdClass) {
            throw new PublishException('Could not obtain user info, the server returned invalid JSON data');
        }

        $userName = $data->name ?? null;
        if (empty($userName) || !is_string($userName)) {
            throw new PublishException('Could not obtain user info, the server returned no user name');
        }

        return $userName;
    }
}
