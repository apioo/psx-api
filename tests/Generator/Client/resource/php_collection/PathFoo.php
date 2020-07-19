<?php 
/**
 * PathFoo generated on 0000-00-00
 * @see https://github.com/apioo
 */

namespace Foo\Bar;


class PathFoo
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
}