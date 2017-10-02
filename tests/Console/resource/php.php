<?php

namespace PSX\Generation;

use PSX\Framework\Controller\SchemaApiAbstract;
/**
 * @Title("Test")
 * @Description("Test description")
 * @PathParam(name="fooId", type="string", required=true)
 */
class Endpoint extends SchemaApiAbstract
{
    /**
     * @Description("A long **Test** description")
     * @QueryParam(name="foo", type="string", description="Test")
     * @QueryParam(name="bar", type="string", required=true)
     * @QueryParam(name="baz", type="string", enum={"foo", "bar"})
     * @QueryParam(name="boz", type="string", pattern="[A-z]+")
     * @QueryParam(name="integer", type="integer")
     * @QueryParam(name="number", type="number")
     * @QueryParam(name="date", type="string")
     * @QueryParam(name="boolean", type="boolean")
     * @QueryParam(name="string", type="string")
     * @Incoming(schema="PSX\Generation\ObjectId")
     * @Outgoing(code=200, schema="PSX\Generation\ObjectId")
     */
    public function doGet($record)
    {
    }
}
namespace PSX\Generation;

/**
 * @Description("A canonical song")
 * @Required({"title", "artist"})
 */
class ObjectId
{
    /**
     * @Key("artist")
     * @Type("string")
     */
    protected $artist;
    /**
     * @Key("title")
     * @Type("string")
     */
    protected $title;
    public function setArtist($artist)
    {
        $this->artist = $artist;
    }
    public function getArtist()
    {
        return $this->artist;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function getTitle()
    {
        return $this->title;
    }
}