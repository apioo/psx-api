<?php
/**
 * FooByNameAndTypeResource generated on 0000-00-00
 * @see https://sdkgen.app
 */


use GuzzleHttp\Client;
use PSX\Schema\SchemaManager;
use Sdkgen\Client\ResourceAbstract;

class FooByNameAndTypeResource extends ResourceAbstract
{
    private string $url;

    private string $name;
    private string $type;

    public function __construct(string $name, string $type, string $baseUrl, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {
        parent::__construct($baseUrl, $httpClient, $schemaManager);

        $this->name = $name;
        $this->type = $type;
        $this->url = $this->baseUrl . '/foo/' . $name . '/' . $type . '';
    }

    /**
     * Returns a collection
     *
     * @param GetQuery|null $query
     * @return EntryCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listFoo(?GetQuery $query = null): EntryCollection
    {
        $options = [
            'query' => $query !== null ? (array) $query->jsonSerialize() : [],
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
    public function createFoo(EntryCreate $data): EntryMessage
    {
        $options = [
            'json' => $data
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryMessage::class);
    }

    /**
     * @param EntryUpdate $data
     * @return EntryMessage
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(EntryUpdate $data): EntryMessage
    {
        $options = [
            'json' => $data
        ];

        $response = $this->httpClient->request('PUT', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryMessage::class);
    }

    /**
     * @return EntryMessage
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(): EntryMessage
    {
        $options = [
        ];

        $response = $this->httpClient->request('DELETE', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryMessage::class);
    }

    /**
     * @param EntryPatch $data
     * @return EntryMessage
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch(EntryPatch $data): EntryMessage
    {
        $options = [
            'json' => $data
        ];

        $response = $this->httpClient->request('PATCH', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryMessage::class);
    }

}
