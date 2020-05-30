<?php 
/**
 * Collection generated on 0000-00-00
 * @see https://github.com/apioo
 */

namespace Foo\Bar;


class Collection
{
    /**
     * @var array<Item>|null
     */
    protected $entry;
    /**
     * @param array<Item>|null $entry
     */
    public function setEntry(?array $entry) : void
    {
        $this->entry = $entry;
    }
    /**
     * @return array<Item>|null
     */
    public function getEntry() : ?array
    {
        return $this->entry;
    }
}