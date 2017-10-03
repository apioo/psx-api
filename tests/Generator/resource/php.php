<?php

namespace PSX\Generation;

use PSX\Framework\Controller\SchemaApiAbstract;
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
    public function doGet($record)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Item")
     * @Outgoing(code=201, schema="PSX\Generation\Message")
     */
    public function doPost($record)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Item")
     * @Outgoing(code=200, schema="PSX\Generation\Message")
     */
    public function doPut($record)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Item")
     * @Outgoing(code=200, schema="PSX\Generation\Message")
     */
    public function doDelete($record)
    {
    }
    /**
     * @Incoming(schema="PSX\Generation\Item")
     * @Outgoing(code=200, schema="PSX\Generation\Message")
     */
    public function doPatch($record)
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
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
    public function getUserId()
    {
        return $this->userId;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function setDate($date)
    {
        $this->date = $date;
    }
    public function getDate()
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
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }
    public function getEntry()
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
    public function setSuccess($success)
    {
        $this->success = $success;
    }
    public function getSuccess()
    {
        return $this->success;
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