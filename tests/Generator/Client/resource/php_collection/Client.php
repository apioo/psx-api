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
     * @return BarByFooResource
     */
    public function getBarByFoo(?string $foo): BarByFooResource
    {
        return new BarByFooResource(
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
     * @return BarByYearResource
     */
    public function getBarByYear(?string $year): BarByYearResource
    {
        return new BarByYearResource(
            $year,
            $this->baseUrl,
            $this->token,
            $this->httpClient,
            $this->schemaManager
        );
    }

}
