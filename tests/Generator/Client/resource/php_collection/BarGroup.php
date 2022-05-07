<?php
/**
 * BarGroup generated on 0000-00-00
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
