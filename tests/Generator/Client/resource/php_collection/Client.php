<?php 
/**
 * Client generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

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
     * Tag: foo
     *
     * @return FooGroup
     */
    public function foo(): FooGroup
    {
        return new FooGroup(
            $this->baseUrl,
            $this->newHttpClient($this->credentials),
            $this->schemaManager
        );
    }

    /**
     * Tag: bar
     *
     * @return BarGroup
     */
    public function bar(): BarGroup
    {
        return new BarGroup(
            $this->baseUrl,
            $this->newHttpClient($this->credentials),
            $this->schemaManager
        );
    }

}
