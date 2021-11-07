<?php 
/**
 * Client generated on 0000-00-00
 * @see https://github.com/apioo
 */


use Sdkgen\Client\ClientAbstract;
use Sdkgen\Client\Credentials;

class Client extends ClientAbstract
{
    public function __construct(string $baseUri, ?TokenStoreInterface $tokenStore = null)
    {
        parent::__construct($baseUri, $tokenStore);

    }

    /**
     * Endpoint: /foo/:name/:type
     *
     * @return FooByNameAndTypeResource
     */
    public function getFooByNameAndType(?string $name, ?string $type): FooByNameAndTypeResource
    {
        return new FooByNameAndTypeResource(
            $name,
            $type,
            $this->baseUrl,
            $this->getAccessToken(),
            $this->httpClient,
            $this->schemaManager
        );
    }

}
