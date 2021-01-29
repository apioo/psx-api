<?php 
/**
 * FooByNameAndTypeResource generated on 0000-00-00
 * @see https://github.com/apioo
 */

namespace Foo\Bar;

use GuzzleHttp\Client;
use PSX\Api\Generator\Client\Php\ResourceAbstract;
use PSX\Schema\SchemaManager;

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
     * @param Entry|EntryMessage $data
     * @return Entry|EntryMessage
     */
    public function postEntryOrMessage($data = null)
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'json' => $data
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, null);
    }

}
