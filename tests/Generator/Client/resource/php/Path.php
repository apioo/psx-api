<?php 
/**
 * Path generated on 0000-00-00
 * @see https://github.com/apioo
 */

/**
 * @Required({"name"})
 */
class Path
{
    /**
     * @var string|null
     * @Description("Name parameter")
     * @Pattern("[A-z]+")
     * @MinLength(0)
     * @MaxLength(16)
     */
    protected $name;
    /**
     * @var string|null
     * @Enum({"foo", "bar"})
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
}