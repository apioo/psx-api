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
     * Endpoint: /foo
     *
     * @return FooResource
     */
    public function getFoo(): FooResource
    {
        return new FooResource(
            $this->baseUrl,
            $this->token,
            $this->httpClient,
            $this->schemaManager
        );
    }

    /**
     * Endpoint: /bar/:foo
     *
     * @return BarFooResource
     */
    public function getBarFoo(?string $foo): BarFooResource
    {
        return new BarFooResource(
            $foo,
            $this->baseUrl,
            $this->token,
            $this->httpClient,
            $this->schemaManager
        );
    }

    /**
     * Endpoint: /bar/$year<[0-9]+>
     *
     * @return BarYear09Resource
     */
    public function getBarYear09(?string $year): BarYear09Resource
    {
        return new BarYear09Resource(
            $year,
            $this->baseUrl,
            $this->token,
            $this->httpClient,
            $this->schemaManager
        );
    }

}
