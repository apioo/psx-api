<?php
/**
 * ImportMyTypeException automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use Sdkgen\Client\Exception\KnownStatusCodeException;

class ImportMyTypeException extends KnownStatusCodeException
{
    private \External\Bar\MyType $payload;

    public function __construct(\External\Bar\MyType $payload)
    {
        parent::__construct('The server returned an error');

        $this->payload = $payload;
    }

    /**
     * @return \External\Bar\MyType
     */
    public function getPayload(): \External\Bar\MyType
    {
        return $this->payload;
    }
}
