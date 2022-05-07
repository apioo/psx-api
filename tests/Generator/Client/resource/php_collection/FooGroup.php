<?php
/**
 * FooGroup generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use Sdkgen\Client\ResourceAbstract;

class FooGroup extends ResourceAbstract
{
    /**
     * Endpoint: /foo
     */
    public function getFoo(): FooResource
    {
        return new FooResource(
            $this->baseUrl,
            $this->httpClient,
            $this->schemaManager
        );
    }

}
