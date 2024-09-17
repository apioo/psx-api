<?php
/**
 * Client automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */


use GuzzleHttp\Exception\BadResponseException;
use Sdkgen\Client\ClientAbstract;
use Sdkgen\Client\Credentials;
use Sdkgen\Client\CredentialsInterface;
use Sdkgen\Client\Exception\ClientException;
use Sdkgen\Client\Exception\Payload;
use Sdkgen\Client\Exception\UnknownStatusCodeException;
use Sdkgen\Client\TokenStoreInterface;

class Client extends ClientAbstract
{
    /**
     * Returns a collection
     *
     * @param string $name
     * @param string $type
     * @param int|null $startIndex
     * @param float|null $float
     * @param bool|null $boolean
     * @param \PSX\DateTime\LocalDate|null $date
     * @param \PSX\DateTime\LocalDateTime|null $datetime
     * @param Entry|null $args
     * @return EntryCollection
     * @throws ClientException
     */
    public function get(string $name, string $type, ?int $startIndex = null, ?float $float = null, ?bool $boolean = null, ?\PSX\DateTime\LocalDate $date = null, ?\PSX\DateTime\LocalDateTime $datetime = null, ?Entry $args = null): EntryCollection
    {
        $url = $this->parser->url('/foo/:name/:type', [
            'name' => $name,
            'type' => $type,
        ]);

        $options = [
            'headers' => [
            ],
            'query' => $this->parser->query([
                'startIndex' => $startIndex,
                'float' => $float,
                'boolean' => $boolean,
                'date' => $date,
                'datetime' => $datetime,
                'args' => $args,
            ], [
                'args',
            ]),
        ];

        try {
            $response = $this->httpClient->request('GET', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, EntryCollection::class);

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param EntryCreate $payload
     * @return EntryMessage
     * @throws EntryMessageException
     * @throws ClientException
     */
    public function create(string $name, string $type, EntryCreate $payload): EntryMessage
    {
        $url = $this->parser->url('/foo/:name/:type', [
            'name' => $name,
            'type' => $type,
        ]);

        $options = [
            'headers' => [
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'json' => $payload
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, EntryMessage::class);

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                case $statusCode === 400:
                    $data = $this->parser->parse((string) $body, EntryMessage::class);

                    throw new EntryMessageException($data);
                case $statusCode === 500:
                    $data = $this->parser->parse((string) $body, EntryMessage::class);

                    throw new EntryMessageException($data);
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param \PSX\Record\Record<EntryUpdate> $payload
     * @return \PSX\Record\Record<EntryMessage>
     * @throws EntryMessageException
     * @throws MapEntryMessageException
     * @throws ClientException
     */
    public function update(string $name, string $type, \PSX\Record\Record $payload): \PSX\Record\Record
    {
        $url = $this->parser->url('/foo/:name/:type', [
            'name' => $name,
            'type' => $type,
        ]);

        $options = [
            'headers' => [
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'json' => $payload
        ];

        try {
            $response = $this->httpClient->request('PUT', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, EntryMessage::class, isMap: true);

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                case $statusCode === 400:
                    $data = $this->parser->parse((string) $body, EntryMessage::class, isMap: true);

                    throw new EntryMessageException($data);
                case $statusCode === 500:
                    $data = $this->parser->parse((string) $body, EntryMessage::class, isMap: true);

                    throw new MapEntryMessageException($data);
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @return void
     * @throws ClientException
     */
    public function delete(string $name, string $type): void
    {
        $url = $this->parser->url('/foo/:name/:type', [
            'name' => $name,
            'type' => $type,
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

        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param array<EntryPatch> $payload
     * @return array<EntryMessage>
     * @throws EntryMessageException
     * @throws ArrayEntryMessageException
     * @throws ClientException
     */
    public function patch(string $name, string $type, array $payload): array
    {
        $url = $this->parser->url('/foo/:name/:type', [
            'name' => $name,
            'type' => $type,
        ]);

        $options = [
            'headers' => [
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'json' => $payload
        ];

        try {
            $response = $this->httpClient->request('PATCH', $url, $options);
            $body = $response->getBody();

            $data = $this->parser->parse((string) $body, EntryMessage::class, isArray: true);

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                case $statusCode === 400:
                    $data = $this->parser->parse((string) $body, EntryMessage::class, isArray: true);

                    throw new EntryMessageException($data);
                case $statusCode === 500:
                    $data = $this->parser->parse((string) $body, EntryMessage::class, isArray: true);

                    throw new ArrayEntryMessageException($data);
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }




    public static function build(string $token): self
    {
        return new self('http://api.foo.com', new Credentials\HttpBearer($token));
    }

    public static function buildAnonymous(): self
    {
        return new self('http://api.foo.com', new Credentials\Anonymous());
    }
}
