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
use Sdkgen\Client\Exception\UnknownStatusCodeException;

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
     * @return EntryCollection
     * @throws ClientException
     */
    public function get(string $name, string $type, ?int $startIndex = null, ?float $float = null, ?bool $boolean = null, ?\PSX\DateTime\LocalDate $date = null, ?\PSX\DateTime\LocalDateTime $datetime = null): EntryCollection
    {
        $url = $this->parser->url('/foo/:name/:type', [
            'name' => $name,
            'type' => $type,
        ]);

        $options = [
            'query' => $this->parser->query([
                'startIndex' => $startIndex,
                'float' => $float,
                'boolean' => $boolean,
                'date' => $date,
                'datetime' => $datetime,
            ]),
        ];

        try {
            $response = $this->httpClient->request('GET', $url, $options);
            $data = (string) $response->getBody();

            return $this->parser->parse($data, EntryCollection::class);
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $data = (string) $e->getResponse()->getBody();

            switch ($e->getResponse()->getStatusCode()) {
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
            'query' => $this->parser->query([
            ]),
            'json' => $payload
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $data = (string) $response->getBody();

            return $this->parser->parse($data, EntryMessage::class);
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $data = (string) $e->getResponse()->getBody();

            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new EntryMessageException($this->parser->parse($data, EntryMessage::class));
                case 500:
                    throw new EntryMessageException($this->parser->parse($data, EntryMessage::class));
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
            'query' => $this->parser->query([
            ]),
            'json' => $payload
        ];

        try {
            $response = $this->httpClient->request('PUT', $url, $options);
            $data = (string) $response->getBody();

            return $this->parser->parse($data, EntryMessage::class, isMap: true);
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $data = (string) $e->getResponse()->getBody();

            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new EntryMessageException($this->parser->parse($data, EntryMessage::class));
                case 500:
                    throw new MapEntryMessageException($this->parser->parse($data, EntryMessage::class, isMap: true));
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
            'query' => $this->parser->query([
            ]),
        ];

        try {
            $response = $this->httpClient->request('DELETE', $url, $options);
            $data = (string) $response->getBody();

        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $data = (string) $e->getResponse()->getBody();

            switch ($e->getResponse()->getStatusCode()) {
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
            'query' => $this->parser->query([
            ]),
            'json' => $payload
        ];

        try {
            $response = $this->httpClient->request('PATCH', $url, $options);
            $data = (string) $response->getBody();

            return $this->parser->parse($data, EntryMessage::class, isArray: true);
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $data = (string) $e->getResponse()->getBody();

            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new EntryMessageException($this->parser->parse($data, EntryMessage::class));
                case 500:
                    throw new ArrayEntryMessageException($this->parser->parse($data, EntryMessage::class, isArray: true));
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
}
