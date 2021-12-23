<?php
/**
 * Path generated on 0000-00-00
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use PSX\Schema\Attribute\Required;

#[Required(array('name', 'type'))]
class Path implements \JsonSerializable
{
    protected ?string $name = null;
    protected ?string $type = null;
    public function setName(?string $name) : void
    {
        $this->name = $name;
    }
    public function getName() : ?string
    {
        return $this->name;
    }
    public function setType(?string $type) : void
    {
        $this->type = $type;
    }
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
