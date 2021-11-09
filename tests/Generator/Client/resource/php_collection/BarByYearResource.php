<?php 
/**
 * BarByYearResource generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use GuzzleHttp\Client;
use PSX\Schema\SchemaManager;
use Sdkgen\Client\ResourceAbstract;

class BarByYearResource extends ResourceAbstract
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $year;

    public function __construct(string $year, string $baseUrl, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {
        parent::__construct($baseUrl, $httpClient, $schemaManager);

        $this->year = $year;
        $this->url = $this->baseUrl . '/bar/' . $year . '';
    }

    /**
     * Returns a collection
     *
     * @return EntryCollection
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
     */
    public function post(?EntryCreate $data = null): EntryMessage
    {
        $options = [
            'json' => $data
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryMessage::class);
    }

}
