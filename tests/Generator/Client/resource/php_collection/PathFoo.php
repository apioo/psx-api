<?php 
/**
 * PathFoo generated on 0000-00-00
 * @see https://github.com/apioo
 */

namespace Foo\Bar;


class PathFoo implements \JsonSerializable
{
    /**
     * @var string|null
     */
    protected $foo;
    /**
     * @param string|null $foo
     */
    public function setFoo(?string $foo) : void
    {
        $this->foo = $foo;
    }
    /**
     * @return string|null
     */
    public function getFoo() : ?string
    {
        return $this->foo;
    }
    public function jsonSerialize()
    {
        return (object) array_filter(array('foo' => $this->foo), static function ($value) : bool {
            return $value !== null;
        });
    }
}