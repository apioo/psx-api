<?php
/**
 * FooByNameAndTypeResource generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

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
     * @param Entry|EntryMessage $data
     * @return Entry|EntryMessage
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postEntryOrMessage(Entry|EntryMessage $data): Entry|EntryMessage
    {
        $options = [
            'json' => $data
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, Entry|EntryMessage::class);
    }

}
