<?php
/**
 * FooBarTag automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use GuzzleHttp\Exception\BadResponseException;
use Sdkgen\Client\Exception\ClientException;
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
            'query' => $this->parser->query([
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


}
