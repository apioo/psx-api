<?php
/**
 * BarByFooResource generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use GuzzleHttp\Client;
use PSX\Schema\SchemaManager;
use Sdkgen\Client\ResourceAbstract;

class BarByFooResource extends ResourceAbstract
{
    private string $url;

    private string $foo;

    public function __construct(string $foo, string $baseUrl, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {
        parent::__construct($baseUrl, $httpClient, $schemaManager);

        $this->foo = $foo;
        $this->url = $this->baseUrl . '/bar/' . $foo . '';
    }

    /**
     * Returns a collection
     *
     * @return EntryCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(): EntryCollection
    {
        $options = [
        ];

        $response = $this->httpClient->request('GET', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryCollection::class);
    }

    /**
     * @param EntryCreate $data
     * @return EntryMessage
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(EntryCreate $data): EntryMessage
    {
        $options = [
            'json' => $data
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryMessage::class);
    }

}
