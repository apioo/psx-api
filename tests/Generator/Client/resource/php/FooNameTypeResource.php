<?php 
/**
 * FooNameTypeResource generated on 0000-00-00
 * @see https://github.com/apioo
 */


use GuzzleHttp\Client;
use PSX\Api\Generator\Client\Php\ResourceAbstract;
use PSX\Schema\SchemaManager;

class FooNameTypeResource extends ResourceAbstract
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

    public function __construct(string $name, string $type, string $baseUrl, string $token, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {
        parent::__construct($baseUrl, $token, $httpClient, $schemaManager);

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
    public function listFoo(?GetQuery $query): EntryCollection
    {
        $options = [
            'query' => $this->prepare($query, true),
        ];

        $response = $this->httpClient->request('GET', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryCollection::class);
    }

    /**
     * @param EntryCreate $data
     * @return EntryMessage
     */
    public function createFoo(?EntryCreate $data): EntryMessage
    {
        $options = [
            'json' => $this->prepare($data)
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryMessage::class);
    }

    /**
     * @param EntryUpdate $data
     * @return EntryMessage
     */
    public function put(?EntryUpdate $data): EntryMessage
    {
        $options = [
            'json' => $this->prepare($data)
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
    public function patch(?EntryPatch $data): EntryMessage
    {
        $options = [
            'json' => $this->prepare($data)
        ];

        $response = $this->httpClient->request('PATCH', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryMessage::class);
    }

}
