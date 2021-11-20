<?php 
/**
 * Client generated on 0000-00-00
 * @see https://sdkgen.app
 */


use Sdkgen\Client\ClientAbstract;
use Sdkgen\Client\Credentials;
use Sdkgen\Client\TokenStoreInterface;

class Client extends ClientAbstract
{
    public function __construct(string $token, string $baseUrl, ?TokenStoreInterface $tokenStore = null)
    {
        parent::__construct($baseUrl, $tokenStore);

        $this->credentials = new Credentials\HttpBearer($token);
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
            $this->newHttpClient($this->credentials),
            $this->schemaManager
        );
    }

}