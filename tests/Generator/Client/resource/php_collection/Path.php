<?php 
/**
 * Path generated on 0000-00-00
 * @see https://github.com/apioo
 */

namespace Foo\Bar;


class Path
{
    /**
     * @var string|null
     */
    protected $year;
    /**
     * @param string|null $year
     */
    public function setYear(?string $year) : void
    {
        $this->year = $year;
    }
    /**
     * @return string|null
     */
    public function getYear() : ?string
    {
        return $this->year;
    }
}