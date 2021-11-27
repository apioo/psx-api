<?php
/**
 * Path generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

/**
 * @Required({"name", "type"})
 */
class Path implements \JsonSerializable
{
    /**
     * @var string|null
     */
    protected $name;
    /**
     * @var string|null
     */
    protected $type;
    /**
     * @param string|null $name
     */
    public function setName(?string $name) : void
    {
        $this->name = $name;
    }
    /**
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    /**
     * @param string|null $type
     */
    public function setType(?string $type) : void
    {
        $this->type = $type;
    }
    /**
     * @return string|null
     */
    public function getType() : ?string
    {
        return $this->type;
    }
    public function jsonSerialize()
    {
        return (object) array_filter(array('name' => $this->name, 'type' => $this->type), static function ($value) : bool {
            return $value !== null;
        });
    }
}
