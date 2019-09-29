<?php

namespace PSX\Generation;

use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Http\Environment\HttpContextInterface;
/**
 * @Title("foo")
 * @Description("lorem ipsum")
 * @PathParam(name="name", type="string", description="Name parameter", required=true, minLength=0, maxLength=16, pattern="[A-z]+")
 * @PathParam(name="type", type="string", enum={"foo", "bar"})
 */
class Endpoint extends SchemaApiAbstract
{
    /**
     * @Description("Returns a collection")
     * @QueryParam(name="startIndex", type="integer", description="startIndex parameter", required=true, minimum=0, maximum=32)
     * @QueryParam(name="float", type="number")
     * @QueryParam(name="boolean", type="boolean")
     * @QueryParam(name="date", type="string", format="date")
     * @QueryParam(name="datetime", type="string", format="date-time")
     * @Outgoing(code=200, schema="PSX\Generation\Collection")
     */
    public function doGet(HttpContextInterface $context)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Item")
     * @Outgoing(code=201, schema="PSX\Generation\Message")
     */
    public function doPost($record, HttpContextInterface $context)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Item")
     * @Outgoing(code=200, schema="PSX\Generation\Message")
     */
    public function doPut($record, HttpContextInterface $context)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Item")
     * @Outgoing(code=200, schema="PSX\Generation\Message")
     */
    public function doDelete($record, HttpContextInterface $context)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Item")
     * @Outgoing(code=200, schema="PSX\Generation\Message")
     */
    public function doPatch($record, HttpContextInterface $context)
    {
    }
}
namespace PSX\Generation;

/**
 * @Title("item")
 */
class Item
{
    /**
     * @Key("id")
     * @Type("integer")
     */
    protected $id;
    /**
     * @Key("userId")
     * @Type("integer")
     */
    protected $userId;
    /**
     * @Key("title")
     * @Type("string")
     * @MaxLength(16)
     * @MinLength(3)
     * @Pattern("[A-z]+")
     */
    protected $title;
    /**
     * @Key("date")
     * @Type("string")
     * @Format("date-time")
     */
    protected $date;
    public function setId(?int $id)
    {
        $this->id = $id;
    }
    public function getId() : ?int
    {
        return $this->id;
    }
    public function setUserId(?int $userId)
    {
        $this->userId = $userId;
    }
    public function getUserId() : ?int
    {
        return $this->userId;
    }
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }
    public function getTitle() : ?string
    {
        return $this->title;
    }
    public function setDate(?\DateTime $date)
    {
        $this->date = $date;
    }
    public function getDate() : ?\DateTime
    {
        return $this->date;
    }
}
/**
 * @Title("collection")
 */
class Collection
{
    /**
     * @Key("entry")
     * @Type("array")
     * @Items(@Ref("PSX\Generation\Item"))
     */
    protected $entry;
    public function setEntry(?array $entry)
    {
        $this->entry = $entry;
    }
    public function getEntry() : ?array
    {
        return $this->entry;
    }
}
namespace PSX\Generation;

/**
 * @Title("message")
 */
class Message
{
    /**
     * @Key("success")
     * @Type("boolean")
     */
    protected $success;
    /**
     * @Key("message")
     * @Type("string")
     */
    protected $message;
    public function setSuccess(?bool $success)
    {
        $this->success = $success;
    }
    public function getSuccess() : ?bool
    {
        return $this->success;
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