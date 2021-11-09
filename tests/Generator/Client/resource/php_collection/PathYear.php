<?php 
/**
 * PathYear generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;


class PathYear implements \JsonSerializable
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
    public function jsonSerialize()
    {
        return (object) array_filter(array('year' => $this->year), static function ($value) : bool {
            return $value !== null;
        });
    }
}
