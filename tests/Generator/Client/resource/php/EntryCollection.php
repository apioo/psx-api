<?php
/**
 * EntryCollection generated on 0000-00-00
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
    public function jsonSerialize() : \stdClass
    {
        return (object) array_filter(array('entry' => $this->entry), static function ($value) : bool {
            return $value !== null;
        });
    }
}
