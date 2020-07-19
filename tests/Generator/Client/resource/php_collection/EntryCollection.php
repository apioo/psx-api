<?php 
/**
 * EntryCollection generated on 0000-00-00
 * @see https://github.com/apioo
 */

namespace Foo\Bar;


class EntryCollection
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
}