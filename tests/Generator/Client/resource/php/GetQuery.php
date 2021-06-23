<?php 
/**
 * GetQuery generated on 0000-00-00
 * @see https://github.com/apioo
 */

/**
 * @Required({"startIndex"})
 */
class GetQuery implements \JsonSerializable
{
    /**
     * @var int|null
     * @Description("startIndex parameter")
     * @Minimum(0)
     * @Maximum(32)
     */
    protected $startIndex;
    /**
     * @var float|null
     */
    protected $float;
    /**
     * @var bool|null
     */
    protected $boolean;
    /**
     * @var \PSX\DateTime\Date|null
     */
    protected $date;
    /**
     * @var \DateTime|null
     */
    protected $datetime;
    /**
     * @param int|null $startIndex
     */
    public function setStartIndex(?int $startIndex) : void
    {
        $this->startIndex = $startIndex;
    }
    /**
     * @return int|null
     */
    public function getStartIndex() : ?int
    {
        return $this->startIndex;
    }
    /**
     * @param float|null $float
     */
    public function setFloat(?float $float) : void
    {
        $this->float = $float;
    }
    /**
     * @return float|null
     */
    public function getFloat() : ?float
    {
        return $this->float;
    }
    /**
     * @param bool|null $boolean
     */
    public function setBoolean(?bool $boolean) : void
    {
        $this->boolean = $boolean;
    }
    /**
     * @return bool|null
     */
    public function getBoolean() : ?bool
    {
        return $this->boolean;
    }
    /**
     * @param \PSX\DateTime\Date|null $date
     */
    public function setDate(?\PSX\DateTime\Date $date) : void
    {
        $this->date = $date;
    }
    /**
     * @return \PSX\DateTime\Date|null
     */
    public function getDate() : ?\PSX\DateTime\Date
    {
        return $this->date;
    }
    /**
     * @param \DateTime|null $datetime
     */
    public function setDatetime(?\DateTime $datetime) : void
    {
        $this->datetime = $datetime;
    }
    /**
     * @return \DateTime|null
     */
    public function getDatetime() : ?\DateTime
    {
        return $this->datetime;
    }
    public function jsonSerialize()
    {
        return (object) array_filter(array('startIndex' => $this->startIndex, 'float' => $this->float, 'boolean' => $this->boolean, 'date' => $this->date, 'datetime' => $this->datetime), static function ($value) : bool {
            return $value !== null;
        });
    }
}
