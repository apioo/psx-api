<?php
/**
 * MultipartException automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Sdkgen\Client\Tests\Generated;

use Sdkgen\Client\Exception\KnownStatusCodeException;

class MultipartException extends KnownStatusCodeException
{
    private \Sdkgen\Client\Multipart $payload;

    public function __construct(\Sdkgen\Client\Multipart $payload)
    {
        parent::__construct('The server returned an error');

        $this->payload = $payload;
    }

    /**
     * @return \Sdkgen\Client\Multipart
     */
    public function getPayload(): \Sdkgen\Client\Multipart
    {
        return $this->payload;
    }
}