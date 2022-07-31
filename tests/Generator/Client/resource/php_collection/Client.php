<?php
/**
 * Client automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

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
     * Tag: foo
     */
    public function foo(): FooGroup
    {
        return new FooGroup(
            $this->baseUrl,
            $this->newHttpClient(),
            $this->schemaManager
        );
    }

    /**
     * Tag: bar
     */
    public function bar(): BarGroup
    {
        return new BarGroup(
            $this->baseUrl,
            $this->newHttpClient(),
            $this->schemaManager
        );
    }

}
