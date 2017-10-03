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
     * @Incoming(schema="PSX\Generation\Song")
     * @Outgoing(code=200, schema="PSX\Generation\Song")
     */
    public function doGet($record)
    {
    }
}
namespace PSX\Generation;

/**
 * @Title("Rating")
 */
class Rating
{
    /**
     * @Key("author")
     * @Type("string")
     */
    protected $author;
    /**
     * @Key("rating")
     * @Type("integer")
     */
    protected $rating;
    /**
     * @Key("text")
     * @Type("string")
     */
    protected $text;
    public function setAuthor($author)
    {
        $this->author = $author;
    }
    public function getAuthor()
    {
        return $this->author;
    }
    public function setRating($rating)
    {
        $this->rating = $rating;
    }
    public function getRating()
    {
        return $this->rating;
    }
    public function setText($text)
    {
        $this->text = $text;
    }
    public function getText()
    {
        return $this->text;
    }
}
/**
 * @Title("Song")
 * @Description("A canonical song")
 * @Required({"title", "artist"})
 */
class Song
{
    /**
     * @Key("title")
     * @Type("string")
     */
    protected $title;
    /**
     * @Key("artist")
     * @Type("string")
     */
    protected $artist;
    /**
     * @Key("length")
     * @Type("integer")
     */
    protected $length;
    /**
     * @Key("ratings")
     * @Type("array")
     * @Items(@Ref("PSX\Generation\Rating"))
     */
    protected $ratings;
    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function setArtist($artist)
    {
        $this->artist = $artist;
    }
    public function getArtist()
    {
        return $this->artist;
    }
    public function setLength($length)
    {
        $this->length = $length;
    }
    public function getLength()
    {
        return $this->length;
    }
    public function setRatings($ratings)
    {
        $this->ratings = $ratings;
    }
    public function getRatings()
    {
        return $this->ratings;
    }
}