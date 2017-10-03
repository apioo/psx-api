<?php

namespace PSX\Generation;

use PSX\Framework\Controller\SchemaApiAbstract;
/**
 * @Description("foobar")
 */
class Endpoint extends SchemaApiAbstract
{
    /**
     * @Description("List all pets")
     * @QueryParam(name="limit", type="integer", format="int32")
     * @Outgoing(code=200, schema="PSX\Generation\Pets")
     * @Outgoing(code=500, schema="PSX\Generation\Error")
     */
    public function doGet($record)
    {
    }
    /**
     * @Description("Create a pet")
     * @Incoming(schema="PSX\Generation\Pet")
     * @Outgoing(code=500, schema="PSX\Generation\Error")
     */
    public function doPost($record)
    {
    }
}
namespace PSX\Generation;

/**
 * @Title("Pet")
 * @Required({"id", "name"})
 */
class Pet
{
    /**
     * @Key("id")
     * @Type("integer")
     * @Format("int64")
     */
    protected $id;
    /**
     * @Key("name")
     * @Type("string")
     */
    protected $name;
    /**
     * @Key("tag")
     * @Type("string")
     */
    protected $tag;
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setTag($tag)
    {
        $this->tag = $tag;
    }
    public function getTag()
    {
        return $this->tag;
    }
}
/**
 * @Title("Pets")
 */
class Pets
{
    /**
     * @Key("pets")
     * @Type("array")
     * @Items(@Ref("PSX\Generation\Pet"))
     */
    protected $pets;
    public function setPets($pets)
    {
        $this->pets = $pets;
    }
    public function getPets()
    {
        return $this->pets;
    }
}
namespace PSX\Generation;

/**
 * @Title("Error")
 * @Required({"code", "message"})
 */
class Error
{
    /**
     * @Key("code")
     * @Type("integer")
     * @Format("int32")
     */
    protected $code;
    /**
     * @Key("message")
     * @Type("string")
     */
    protected $message;
    public function setCode($code)
    {
        $this->code = $code;
    }
    public function getCode()
    {
        return $this->code;
    }
    public function setMessage($message)
    {
        $this->message = $message;
    }
    public function getMessage()
    {
        return $this->message;
    }
}