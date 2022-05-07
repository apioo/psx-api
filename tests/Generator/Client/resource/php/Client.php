<?php
/**
 * Client generated on 0000-00-00
 * @see https://sdkgen.app
 */


use Sdkgen\Client\ClientAbstract;
use Sdkgen\Client\Credentials;
use Sdkgen\Client\CredentialsInterface;
use Sdkgen\Client\TokenStoreInterface;

class Client extends ClientAbstract
{
    public function __construct(string $baseUrl, string $token, ?TokenStoreInterface $tokenStore = null, ?array $scopes = null)
    {
        parent::__construct($baseUrl, new Credentials\HttpBearer($token), $tokenStore, $scopes);
    }

    /**
     * Endpoint: /foo/:name/:type
     */
    public function getFooByNameAndType(string $name, string $type): FooByNameAndTypeResource
    {
        return new FooByNameAndTypeResource(
            $name,
            $type,
            $this->baseUrl,
            $this->newHttpClient(),
            $this->schemaManager
        );
    }

}
