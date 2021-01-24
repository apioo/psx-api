<?php 
/**
 * Client generated on 0000-00-00
 * @see https://github.com/apioo
 */


use PSX\Api\Generator\Client\Php\ResourceAbstract;

class Client extends ResourceAbstract
{
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
            $this->token,
            $this->httpClient,
            $this->schemaManager
        );
    }

}
