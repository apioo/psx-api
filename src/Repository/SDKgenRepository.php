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

namespace PSX\Api\Repository;

use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\Generator;
use PSX\Api\Repository\SDKgen\ConfigInterface;
use PSX\Http\Client\Client;
use PSX\Http\Client\ClientInterface;
use PSX\Http\Client\GetRequest;

/**
 * SDKgenRepository
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class SDKgenRepository implements RepositoryInterface
{
    public const CLIENT_GO = 'client-go';
    public const CLIENT_JAVA = 'client-java';

    private ConfigInterface $config;
    private ClientInterface $httpClient;
    private ?CacheItemPoolInterface $cache;

    public function __construct(ConfigInterface $config, ?ClientInterface $httpClient = null, ?CacheItemPoolInterface $cache = null)
    {
        $this->config = $config;
        $this->httpClient = $httpClient ?? new Client();
        $this->cache = $cache;
    }

    public function getAll(): array
    {
        $return = [];
        $types = $this->getTypes();
        foreach ($types as $type) {
            [$name, $fileExtension, $mime] = $type;

            $return[$name] = new GeneratorConfig(
                fn(string $baseUrl, string $config) => new Generator\Proxy\SDKgen($this->httpClient, $this->config->getAccessToken(), $name, $baseUrl, $config),
                $fileExtension,
                $mime
            );
        }

        return $return;
    }

    private function getTypes(): array
    {
        $item = $this->cache->getItem('psx-api-generator-types');
        if ($item->isHit()) {
            return $item->get();
        }

        $response = $this->httpClient->request(new GetRequest('https://api.sdkgen.app/generator/types', [
            'Authorization' => $this->config->getAccessToken(),
            'Accept' => 'application/json',
        ]));

        if ($response->getStatusCode() !== 200) {
            return [];
        }

        $data = json_decode((string) $response->getBody());
        if (!$data instanceof \stdClass) {
            return [];
        }

        $types = $this->parse($data);
        if (empty($types)) {
            return [];
        }

        $item->set($types);
        $item->expiresAfter(new \DateInterval('P7D'));
        $this->cache->save($item);

        return $types;
    }

    private function parse(mixed $data): array
    {
        $types = $data->types ?? [];
        if (!is_array($types)) {
            return [];
        }

        $data = [];
        foreach ($types as $type) {
            if (!$type instanceof \stdClass) {
                continue;
            }

            $name = $type->name ?? null;
            $fileExtension = $type->fileExtension ?? null;
            $mime = $type->mime ?? null;

            if (empty($name) || empty($fileExtension) || empty($mime)) {
                continue;
            }

            $data[] = [$name, $fileExtension, $mime];
        }

        return $data;
    }
}
