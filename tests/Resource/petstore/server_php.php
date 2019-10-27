<?php

namespace PSX\Generation;

use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Http\Environment\HttpContextInterface;
/**
 * @Description("foobar")
 */
class PetsResource extends SchemaApiAbstract
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
<?php

namespace PSX\Generation;

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
    /**
     * @param array<Pet> $pets
     */
    public function setPets(?array $pets)
    {
        $this->pets = $pets;
    }
    /**
     * @return array<Pet>
     */
    public function getPets() : ?array
    {
        return $this->pets;
    }
}
<?php

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
    /**
     * @param int $id
     */
    public function setId(?int $id)
    {
        $this->id = $id;
    }
    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }
    /**
     * @param string $name
     */
    public function setName(?string $name)
    {
        $this->name = $name;
    }
    /**
     * @return string
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    /**
     * @param string $tag
     */
    public function setTag(?string $tag)
    {
        $this->tag = $tag;
    }
    /**
     * @return string
     */
    public function getTag() : ?string
    {
        return $this->tag;
    }
}
<?php

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
    /**
     * @param int $code
     */
    public function setCode(?int $code)
    {
        $this->code = $code;
    }
    /**
     * @return int
     */
    public function getCode() : ?int
    {
        return $this->code;
    }
    /**
     * @param string $message
     */
    public function setMessage(?string $message)
    {
        $this->message = $message;
    }
    /**
     * @return string
     */
    public function getMessage() : ?string
    {
        return $this->message;
    }
}