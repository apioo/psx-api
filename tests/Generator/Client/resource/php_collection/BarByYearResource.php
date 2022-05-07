<?php
/**
 * BarByYearResource generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use GuzzleHttp\Client;
use PSX\Http\Exception\StatusCodeException;
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
     * @throws \PSX\Http\Exception\StatusCodeException
     */
    public function get(): EntryCollection
    {
        $options = [
        ];

        $response = $this->httpClient->request('GET', $this->url, $options);
        $data     = (string) $response->getBody();

        if ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
            StatusCodeException::throwOnRedirection($response);
        } elseif ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            StatusCodeException::throwOnClientError($response);
        } elseif ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            StatusCodeException::throwOnServerError($response);
        }

        return $this->parse($data, EntryCollection::class);
    }

    /**
     * @param EntryCreate|null $data
     * @return EntryMessage
     * @throws \PSX\Http\Exception\StatusCodeException
     */
    public function post(?EntryCreate $data = null): EntryMessage
    {
        $options = [
            'json' => $data
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        if ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
            StatusCodeException::throwOnRedirection($response);
        } elseif ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            StatusCodeException::throwOnClientError($response);
        } elseif ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            StatusCodeException::throwOnServerError($response);
        }

        return $this->parse($data, EntryMessage::class);
    }

}
