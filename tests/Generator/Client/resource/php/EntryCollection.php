<?php
/**
 * EntryCollection automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

class EntryCollection implements \JsonSerializable
{
    /**
     * @var array<Entry>|null
     */
    protected ?array $entry = null;
    /**
     * @param array<Entry>|null $entry
     */
    public function setEntry(?array $entry) : void
    {
        $this->entry = $entry;
    }
    public function getEntry() : ?array
    {
        return $this->entry;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('entry' => $this->entry), static function ($value) : bool {
            return $value !== null;
        });
    }
}
