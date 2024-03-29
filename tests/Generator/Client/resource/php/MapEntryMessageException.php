<?php
/**
 * MapEntryMessageException automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */


use Sdkgen\Client\Exception\KnownStatusCodeException;

class MapEntryMessageException extends KnownStatusCodeException
{
    private \PSX\Record\Record $payload;

    public function __construct(\PSX\Record\Record $payload)
    {
        parent::__construct('The server returned an error');

        $this->payload = $payload;
    }

    /**
     * @return \PSX\Record\Record<EntryMessage>
     */
    public function getPayload(): \PSX\Record\Record
    {
        return $this->payload;
    }
}
