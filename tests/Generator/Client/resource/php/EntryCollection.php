<?php 
/**
 * EntryCollection generated on 0000-00-00
 * @see https://github.com/apioo
 */

class EntryCollection implements \JsonSerializable
{
    /**
     * @var array<Entry>|null
     */
    protected $entry;
    /**
     * @param array<Entry>|null $entry
     */
    public function setEntry(?array $entry) : void
    {
        $this->entry = $entry;
    }
    /**
     * @return array<Entry>|null
     */
    public function getEntry() : ?array
    {
        return $this->entry;
    }
    public function jsonSerialize()
    {
        return (object) array_filter(array('entry' => $this->entry), static function ($value) : bool {
            return $value !== null;
        });
    }
}