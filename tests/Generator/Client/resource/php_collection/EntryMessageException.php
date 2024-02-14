<?php
/**
 * EntryMessageException automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use Sdkgen\Client\Exception\KnownStatusCodeException;

class EntryMessageException extends KnownStatusCodeException
{
    private EntryMessage $payload;

    public function __construct(EntryMessage $payload)
    {
        parent::__construct('The server returned an error');

        $this->payload = $payload;
    }

    /**
     * @return EntryMessage
     */
    public function getPayload(): EntryMessage
    {
        return $this->payload;
    }
}
