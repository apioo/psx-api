<?php 
/**
 * Client generated on 0000-00-00
 * @see https://github.com/apioo
 */

namespace Foo\Bar;

use PSX\Api\Generator\Client\Php\ResourceAbstract;

class Client extends ResourceAbstract
{
    /**
     * Endpoint: /foo/:name/:type
     *
     * @return FooNameTypeResource
     */
    public function getFooNameType(?string $name, ?string $type): FooNameTypeResource
    {
        return new FooNameTypeResource(
            $name,
            $type,
            $this->baseUrl,
            $this->token,
            $this->httpClient,
            $this->schemaManager
        );
    }

}
