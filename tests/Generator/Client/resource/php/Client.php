<?php
/**
 * Client automatically generated by SDKgen please do not edit this file manually
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
     *
     * lorem ipsum
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
