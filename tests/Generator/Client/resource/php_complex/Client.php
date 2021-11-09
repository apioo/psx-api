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
    public function __construct(string $baseUrl, ?TokenStoreInterface $tokenStore = null)
    {
        parent::__construct($baseUrl, $tokenStore);

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
