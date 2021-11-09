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
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

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
     * @param GetQuery $query
     * @return EntryCollection
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
     */
    public function createFoo(?EntryCreate $data = null): EntryMessage
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
     */
    public function put(?EntryUpdate $data = null): EntryMessage
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
     */
    public function patch(?EntryPatch $data = null): EntryMessage
    {
        $options = [
            'json' => $data
        ];

        $response = $this->httpClient->request('PATCH', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryMessage::class);
    }

}
