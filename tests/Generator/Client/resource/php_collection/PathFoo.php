<?php
/**
 * PathFoo generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;


class PathFoo implements \JsonSerializable
{
    protected ?string $foo = null;
    public function setFoo(?string $foo) : void
    {
        $this->foo = $foo;
    }
    public function getFoo() : ?string
    {
        return $this->foo;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('foo' => $this->foo), static function ($value) : bool {
            return $value !== null;
        });
    }
}
