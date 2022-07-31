<?php
/**
 * BarGroup automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use Sdkgen\Client\ResourceAbstract;

class BarGroup extends ResourceAbstract
{
    /**
     * Endpoint: /bar/:foo
     */
    public function getBarByFoo(string $foo): BarByFooResource
    {
        return new BarByFooResource(
            $foo,
            $this->baseUrl,
            $this->httpClient,
            $this->schemaManager
        );
    }

    /**
     * Endpoint: /bar/$year<[0-9]+>
     */
    public function getBarByYear(string $year): BarByYearResource
    {
        return new BarByYearResource(
            $year,
            $this->baseUrl,
            $this->httpClient,
            $this->schemaManager
        );
    }

}
