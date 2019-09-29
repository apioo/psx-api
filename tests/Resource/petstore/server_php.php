<?php

namespace PSX\Generation;

use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Http\Environment\HttpContextInterface;
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
    public function doGet(HttpContextInterface $context)
    {
    }
    /**
     * @Description("Create a pet")
     * @Incoming(schema="PSX\Generation\Pet")
     * @Outgoing(code=500, schema="PSX\Generation\Error")
     */
    public function doPost($record, HttpContextInterface $context)
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
    public function setId(?int $id)
    {
        $this->id = $id;
    }
    public function getId() : ?int
    {
        return $this->id;
    }
    public function setName(?string $name)
    {
        $this->name = $name;
    }
    public function getName() : ?string
    {
        return $this->name;
    }
    public function setTag(?string $tag)
    {
        $this->tag = $tag;
    }
    public function getTag() : ?string
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
    public function setPets(?array $pets)
    {
        $this->pets = $pets;
    }
    public function getPets() : ?array
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
    public function setCode(?int $code)
    {
        $this->code = $code;
    }
    public function getCode() : ?int
    {
        return $this->code;
    }
    public function setMessage(?string $message)
    {
        $this->message = $message;
    }
    public function getMessage() : ?string
    {
        return $this->message;
    }
}