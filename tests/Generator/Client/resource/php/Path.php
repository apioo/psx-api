<?php
/**
 * Path generated on 0000-00-00
 * @see https://sdkgen.app
 */

use PSX\Schema\Attribute\Description;
use PSX\Schema\Attribute\Enum;
use PSX\Schema\Attribute\MaxLength;
use PSX\Schema\Attribute\MinLength;
use PSX\Schema\Attribute\Pattern;
use PSX\Schema\Attribute\Required;

#[Required(array('name'))]
class Path implements \JsonSerializable
{
    #[Description('Name parameter')]
    #[Pattern('[A-z]+')]
    #[MinLength(0)]
    #[MaxLength(16)]
    protected ?string $name = null;
    #[Enum(array('foo', 'bar'))]
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
    public function jsonSerialize() : \stdClass
    {
        return (object) array_filter(array('name' => $this->name, 'type' => $this->type), static function ($value) : bool {
            return $value !== null;
        });
    }
}
