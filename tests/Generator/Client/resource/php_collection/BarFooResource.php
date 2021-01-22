<?php 
/**
 * BarFooResource generated on 0000-00-00
 * @see https://github.com/apioo
 */

namespace Foo\Bar;

use GuzzleHttp\Client;
use PSX\Api\Generator\Client\Php\ResourceAbstract;
use PSX\Schema\SchemaManager;

class BarFooResource extends ResourceAbstract
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $foo;

    public function __construct(string $foo, string $baseUrl, string $token, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {
        parent::__construct($baseUrl, $token, $httpClient, $schemaManager);

        $this->foo = $foo;
        $this->url = $this->baseUrl . '/bar/' . $foo . '';
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
    public function post(?EntryCreate $data): EntryMessage
    {
        $options = [
            'json' => $data
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, EntryMessage::class);
    }

}
