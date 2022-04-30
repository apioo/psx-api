<?php
/**
 * Client generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use Sdkgen\Client\ClientAbstract;
use Sdkgen\Client\Credentials;
use Sdkgen\Client\CredentialsInterface;
use Sdkgen\Client\TokenStoreInterface;

class Client extends ClientAbstract
{
    public function __construct(string $baseUrl, ?CredentialsInterface $credentials = null, ?TokenStoreInterface $tokenStore = null, ?array $scopes = null)
    {
        parent::__construct($baseUrl, $credentials, $tokenStore, $scopes);
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
            $this->newHttpClient(),
            $this->schemaManager
        );
    }

}
