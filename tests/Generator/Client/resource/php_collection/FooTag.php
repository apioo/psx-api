<?php
/**
 * FooTag automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use Sdkgen\Client\TagAbstract;

class FooTag extends TagAbstract
{
    /**
     * Returns a collection
     *
     * @return EntryCollection
     * @throws \Sdkgen\Client\ErrorException
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
     * @param EntryCreate $payload
     * @return EntryMessage
     * @throws \Sdkgen\Client\ErrorException
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
