<?php
/**
 * ProductTag automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Sdkgen\Client\Tests\Generated;

use GuzzleHttp\Exception\BadResponseException;
use Sdkgen\Client\Exception\ClientException;
use Sdkgen\Client\Exception\Payload;
use Sdkgen\Client\Exception\UnknownStatusCodeException;
use Sdkgen\Client\TagAbstract;

class ProductTag extends TagAbstract
{
    /**
     * Returns a collection
     *
     * @param int|null $startIndex
     * @param int|null $count
     * @param string|null $search
     * @return TestResponse
     * @throws ClientException
     */
    public function getAll(?int $startIndex = null, ?int $count = null, ?string $search = null): TestResponse
    {
        $url = $this->parser->url('/anything', [
        ]);

        $options = [
            'headers' => [
            ],
            'query' => $this->parser->query([
                'startIndex' => $startIndex,
                'count' => $count,
                'search' => $search,
            ], [
            ]),
        ];

        try {
            $response = $this->httpClient->request('GET', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Creates a new product
     *
     * @param TestRequest $payload
     * @return TestResponse
     * @throws TestResponseException
     * @throws ClientException
     */
    public function create(TestRequest $payload): TestResponse
    {
        $url = $this->parser->url('/anything', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'json' => $payload,
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode === 500) {
                $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

                throw new TestResponseException($data);
            }

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Updates an existing product
     *
     * @param int $id
     * @param TestRequest $payload
     * @return TestResponse
     * @throws ClientException
     */
    public function update(int $id, TestRequest $payload): TestResponse
    {
        $url = $this->parser->url('/anything/:id', [
            'id' => $id,
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'json' => $payload,
        ];

        try {
            $response = $this->httpClient->request('PUT', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Patches an existing product
     *
     * @param int $id
     * @param TestRequest $payload
     * @return TestResponse
     * @throws ClientException
     */
    public function patch(int $id, TestRequest $payload): TestResponse
    {
        $url = $this->parser->url('/anything/:id', [
            'id' => $id,
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'json' => $payload,
        ];

        try {
            $response = $this->httpClient->request('PATCH', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Deletes an existing product
     *
     * @param int $id
     * @return TestResponse
     * @throws ClientException
     */
    public function delete(int $id): TestResponse
    {
        $url = $this->parser->url('/anything/:id', [
            'id' => $id,
        ]);

        $options = [
            'headers' => [
            ],
            'query' => $this->parser->query([
            ], [
            ]),
        ];

        try {
            $response = $this->httpClient->request('DELETE', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Test binary content type
     *
     * @param \Psr\Http\Message\StreamInterface $payload
     * @return TestResponse
     * @throws BinaryException
     * @throws ClientException
     */
    public function binary(\Psr\Http\Message\StreamInterface $payload): TestResponse
    {
        $url = $this->parser->url('/anything/binary', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/octet-stream',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'body' => $payload,
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode === 500) {
                $data = $body;

                throw new BinaryException($data);
            }

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Test form content type
     *
     * @param array $payload
     * @return TestResponse
     * @throws FormException
     * @throws ClientException
     */
    public function form(array $payload): TestResponse
    {
        $url = $this->parser->url('/anything/form', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'form_params' => $payload,
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode === 500) {
                $data = [];
                parse_str((string) $body, $data);

                throw new FormException($data);
            }

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Test json content type
     *
     * @param mixed $payload
     * @return TestResponse
     * @throws JsonException
     * @throws ClientException
     */
    public function json(mixed $payload): TestResponse
    {
        $url = $this->parser->url('/anything/json', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'json' => $payload,
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode === 500) {
                $data = \json_decode((string) $body);

                throw new JsonException($data);
            }

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Test json content type
     *
     * @param \Sdkgen\Client\Multipart $payload
     * @return TestResponse
     * @throws MultipartException
     * @throws ClientException
     */
    public function multipart(\Sdkgen\Client\Multipart $payload): TestResponse
    {
        $url = $this->parser->url('/anything/multipart', [
        ]);

        $options = [
            'headers' => [
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'multipart' => $payload->getParts(),
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode === 500) {
                // @TODO currently not possible, please create an issue at https://github.com/apioo/psx-api if needed
                $data = new \Sdkgen\Client\Multipart();

                throw new MultipartException($data);
            }

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Test text content type
     *
     * @param string $payload
     * @return TestResponse
     * @throws TextException
     * @throws ClientException
     */
    public function text(string $payload): TestResponse
    {
        $url = $this->parser->url('/anything/text', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'text/plain',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'body' => $payload,
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode === 500) {
                $data = (string) $body;

                throw new TextException($data);
            }

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Test xml content type
     *
     * @param string $payload
     * @return TestResponse
     * @throws XmlException
     * @throws ClientException
     */
    public function xml(string $payload): TestResponse
    {
        $url = $this->parser->url('/anything/xml', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'body' => $payload,
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, \PSX\Schema\SchemaSource::fromClass(TestResponse::class));

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode === 500) {
                $data = (string) $body;

                throw new XmlException($data);
            }

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }



}
