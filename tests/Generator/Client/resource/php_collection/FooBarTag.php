<?php
/**
 * FooBarTag automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use GuzzleHttp\Exception\BadResponseException;
use Sdkgen\Client\Exception\ClientException;
use Sdkgen\Client\Exception\Payload;
use Sdkgen\Client\Exception\UnknownStatusCodeException;
use Sdkgen\Client\TagAbstract;

class FooBarTag extends TagAbstract
{
    /**
     * Returns a collection
     *
     * @return EntryCollection
     * @throws ClientException
     */
    public function get(): EntryCollection
    {
        $url = $this->parser->url('/foo', [
        ]);

        $options = [
            'headers' => [
            ],
            'query' => $this->parser->query([
            ], [
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

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param EntryCreate $payload
     * @return EntryMessage
     * @throws EntryMessageException
     * @throws ClientException
     */
    public function create(EntryCreate $payload): EntryMessage
    {
        $url = $this->parser->url('/foo', [
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

            $data = $this->parser->parse((string) $body, EntryMessage::class);

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            if ($statusCode === 400) {
                $data = $this->parser->parse((string) $body, EntryMessage::class);

                throw new EntryMessageException($data);
            }

            if ($statusCode === 500) {
                $data = $this->parser->parse((string) $body, EntryMessage::class);

                throw new EntryMessageException($data);
            }

            throw new UnknownStatusCodeException('The server returned an unknown status code: ' . $statusCode);
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }



}
