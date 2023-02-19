<?php
/**
 * Client automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */


use Sdkgen\Client\ClientAbstract;
use Sdkgen\Client\Credentials;
use Sdkgen\Client\CredentialsInterface;
use Sdkgen\Client\TokenStoreInterface;

class Client extends ClientAbstract
{
    public function __construct(string $baseUrl, string $token, ?TokenStoreInterface $tokenStore = null, ?array $scopes = null)
    {
        parent::__construct($baseUrl, new Credentials\HttpBearer($token), $tokenStore, $scopes);
    }


    /**
     * Returns a collection
     *
     * @param string $name
     * @param string $type
     * @param int|null $startIndex
     * @param float|null $float
     * @param bool|null $boolean
     * @param \PSX\DateTime\Date|null $date
     * @param \DateTime|null $datetime
     * @return EntryCollection
     * @throws \Sdkgen\Client\ErrorException
     */
    public function get(string $name, string $type, ?int $startIndex = null, ?float $float = null, ?bool $boolean = null, ?\PSX\DateTime\Date $date = null, ?\DateTime $datetime = null): EntryCollection
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
        } catch (BadResponseException $e) {
            $data = (string) $e->getResponse()->getBody();

            switch ($e->getResponse()->getStatusCode()) {
                default:
                    throw new ErrorException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ErrorException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param EntryCreate $payload
     * @return EntryMessage
     * @throws EntryMessageException
     * @throws EntryMessageException
     * @throws \Sdkgen\Client\ErrorException
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
        } catch (BadResponseException $e) {
            $data = (string) $e->getResponse()->getBody();

            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new EntryMessageException($this->parser->parse($data, EntryMessage::class));
                case 500:
                    throw new EntryMessageException($this->parser->parse($data, EntryMessage::class));
                default:
                    throw new ErrorException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ErrorException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param EntryUpdate $payload
     * @return EntryMessage
     * @throws \Sdkgen\Client\ErrorException
     */
    public function update(string $name, string $type, EntryUpdate $payload): EntryMessage
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

            return $this->parser->parse($data, EntryMessage::class);
        } catch (BadResponseException $e) {
            $data = (string) $e->getResponse()->getBody();

            switch ($e->getResponse()->getStatusCode()) {
                default:
                    throw new ErrorException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ErrorException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param EntryDelete $payload
     * @return EntryMessage
     * @throws \Sdkgen\Client\ErrorException
     */
    public function delete(string $name, string $type, EntryDelete $payload): EntryMessage
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
            $response = $this->httpClient->request('DELETE', $url, $options);
            $data = (string) $response->getBody();

            return $this->parser->parse($data, EntryMessage::class);
        } catch (BadResponseException $e) {
            $data = (string) $e->getResponse()->getBody();

            switch ($e->getResponse()->getStatusCode()) {
                default:
                    throw new ErrorException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ErrorException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param string $type
     * @param EntryPatch $payload
     * @return EntryMessage
     * @throws \Sdkgen\Client\ErrorException
     */
    public function patch(string $name, string $type, EntryPatch $payload): EntryMessage
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

            return $this->parser->parse($data, EntryMessage::class);
        } catch (BadResponseException $e) {
            $data = (string) $e->getResponse()->getBody();

            switch ($e->getResponse()->getStatusCode()) {
                default:
                    throw new ErrorException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ErrorException('An unknown error occurred: ' . $e->getMessage());
        }
    }



}
