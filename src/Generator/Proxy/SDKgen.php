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

namespace PSX\Api\Generator\Proxy;

use PSX\Api\Exception\GeneratorException;
use PSX\Api\Generator\ConfigurationAwareInterface;
use PSX\Api\Generator\ConfigurationTrait;
use PSX\Api\Generator\Spec\TypeAPI;
use PSX\Api\GeneratorInterface;
use PSX\Api\SpecificationInterface;
use PSX\Http\Client\ClientInterface;
use PSX\Http\Client\PostRequest;
use PSX\Schema\Generator;
use PSX\Uri\Uri;

/**
 * SDKgen
 *
 * @see     https://sdkgen.app/
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class SDKgen implements GeneratorInterface, ConfigurationAwareInterface
{
    use ConfigurationTrait;

    private ClientInterface $httpClient;
    private string $accessToken;
    private string $type;
    private ?Generator\Config $config;

    public function __construct(ClientInterface $httpClient, string $accessToken, string $type, ?string $baseUrl = null, ?Generator\Config $config = null)
    {
        $this->httpClient = $httpClient;
        $this->accessToken = $accessToken;
        $this->type = $type;
        $this->baseUrl = $baseUrl;
        $this->config = $config;
    }

    public function generate(SpecificationInterface $specification): Generator\Code\Chunks|string
    {
        // transform to TypeAPI spec
        $generator = new TypeAPI($this->getBaseUrl($specification));
        $generator->setSecurity($this->getSecurity($specification));

        $body = $generator->generate($specification);

        $uri = Uri::parse('https://api.sdkgen.app/generate/' . $this->type);
        $uri = $uri->withParameters([
            'base_url' => $this->baseUrl,
            'config' => $this->config?->toString(),
        ]);

        $response = $this->httpClient->request(new PostRequest($uri, [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], $body));

        if ($response->getStatusCode() !== 200) {
            throw new GeneratorException('Could not generate SDK, got an HTTP response: ' . $response->getStatusCode());
        }

        $data = json_decode((string) $response->getBody());
        if (!$data instanceof \stdClass) {
            throw new GeneratorException('Could not generate SDK, received an invalid JSON payload');
        }

        if (isset($data->chunks) && $data->chunks instanceof \stdClass) {
            return $this->buildChunks($data->chunks);
        } elseif (isset($data->output) && is_string($data->output)) {
            return $data->output;
        } else {
            throw new GeneratorException('Could not generate SDK, received an invalid response');
        }
    }

    private function buildChunks(\stdClass $chunks): Generator\Code\Chunks
    {
        $result = new Generator\Code\Chunks();
        foreach ($chunks as $identifier => $code) {
            if (empty($identifier) || empty($code)) {
                continue;
            }

            if ($code instanceof \stdClass) {
                $result->append($identifier, $this->buildChunks($code));
            } else {
                $result->append($identifier, $code);
            }
        }

        return $result;
    }
}
