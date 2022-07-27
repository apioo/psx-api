<?php
/**
 * GetQuery generated on 0000-00-00
 * @see https://sdkgen.app
 */

use PSX\Schema\Attribute\Description;
use PSX\Schema\Attribute\Maximum;
use PSX\Schema\Attribute\Minimum;
use PSX\Schema\Attribute\Required;

#[Required(array('startIndex'))]
class GetQuery implements \JsonSerializable
{
    #[Description('startIndex parameter')]
    #[Minimum(0)]
    #[Maximum(32)]
    protected ?int $startIndex = null;
    protected ?float $float = null;
    protected ?bool $boolean = null;
    protected ?\PSX\DateTime\Date $date = null;
    protected ?\DateTime $datetime = null;
    public function setStartIndex(?int $startIndex) : void
    {
        $this->startIndex = $startIndex;
    }
    public function getStartIndex() : ?int
    {
        return $this->startIndex;
    }
    public function setFloat(?float $float) : void
    {
        $this->float = $float;
    }
    public function getFloat() : ?float
    {
        return $this->float;
    }
    public function setBoolean(?bool $boolean) : void
    {
        $this->boolean = $boolean;
    }
    public function getBoolean() : ?bool
    {
        return $this->boolean;
    }
    public function setDate(?\PSX\DateTime\Date $date) : void
    {
        $this->date = $date;
    }
    public function getDate() : ?\PSX\DateTime\Date
    {
        return $this->date;
    }
    public function setDatetime(?\DateTime $datetime) : void
    {
        $this->datetime = $datetime;
    }
    public function getDatetime() : ?\DateTime
    {
        return $this->datetime;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('startIndex' => $this->startIndex, 'float' => $this->float, 'boolean' => $this->boolean, 'date' => $this->date, 'datetime' => $this->datetime), static function ($value) : bool {
            return $value !== null;
        });
    }
}
