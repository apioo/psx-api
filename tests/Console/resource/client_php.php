<?php

namespace Foo;

use GuzzleHttp\Client;
use PSX\Json\Parser;
use PSX\Schema\Parser\Popo\Dumper;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaTraverser;
use PSX\Schema\Visitor\TypeVisitor;

class Resource
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $token;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var SchemaManager
     */
    private $schemaManager;

    /**
     * @var string
     */
    private $fooId;

    public function __construct(string $fooId, string $baseUrl, string $token, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {
        $this->fooId = $fooId;

        $this->url = $baseUrl . '/foo';
        $this->token = $token;
        $this->httpClient = $httpClient ? $httpClient : new Client();
        $this->schemaManager = $schemaManager ? $schemaManager : new SchemaManager();
    }

    /**
     * A long **Test** description
     *
     * @param GetQuery $query
     * @return Song
     */
    public function doGet(GetQuery $query): Song
    {
        $options = [
            'query' => $this->convertToArray($query),
        ];

        $response = $this->httpClient->request('GET', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->convertToObject($data, Song::class);
    }

    private function convertToArray($object)
    {
        return (new Dumper())->dump($object);
    }

    private function convertToObject(string $data, ?string $class)
    {
        $data = Parser::decode($data);
        if ($class !== null) {
            $schema = $this->schemaManager->getSchema($class);
            return (new SchemaTraverser())->traverse($data, $schema, new TypeVisitor());
        } else {
            return $data;
        }
    }
}





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
    /**
     * @param string $author
     */
    public function setAuthor(?string $author)
    {
        $this->author = $author;
    }
    /**
     * @return string
     */
    public function getAuthor() : ?string
    {
        return $this->author;
    }
    /**
     * @param int $rating
     */
    public function setRating(?int $rating)
    {
        $this->rating = $rating;
    }
    /**
     * @return int
     */
    public function getRating() : ?int
    {
        return $this->rating;
    }
    /**
     * @param string $text
     */
    public function setText(?string $text)
    {
        $this->text = $text;
    }
    /**
     * @return string
     */
    public function getText() : ?string
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
     * @Items(@Ref("Foo\Rating"))
     */
    protected $ratings;
    /**
     * @param string $title
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }
    /**
     * @return string
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }
    /**
     * @param string $artist
     */
    public function setArtist(?string $artist)
    {
        $this->artist = $artist;
    }
    /**
     * @return string
     */
    public function getArtist() : ?string
    {
        return $this->artist;
    }
    /**
     * @param int $length
     */
    public function setLength(?int $length)
    {
        $this->length = $length;
    }
    /**
     * @return int
     */
    public function getLength() : ?int
    {
        return $this->length;
    }
    /**
     * @param array<Rating> $ratings
     */
    public function setRatings(?array $ratings)
    {
        $this->ratings = $ratings;
    }
    /**
     * @return array<Rating>
     */
    public function getRatings() : ?array
    {
        return $this->ratings;
    }
}
/**
 * @Title("GetQuery")
 * @Required({"bar"})
 */
class GetQuery
{
    /**
     * @Key("foo")
     * @Description("Test")
     * @Type("string")
     */
    protected $foo;
    /**
     * @Key("bar")
     * @Type("string")
     */
    protected $bar;
    /**
     * @Key("baz")
     * @Enum({"foo", "bar"})
     * @Type("string")
     */
    protected $baz;
    /**
     * @Key("boz")
     * @Type("string")
     * @Pattern("[A-z]+")
     */
    protected $boz;
    /**
     * @Key("integer")
     * @Type("integer")
     */
    protected $integer;
    /**
     * @Key("number")
     * @Type("number")
     */
    protected $number;
    /**
     * @Key("date")
     * @Type("string")
     */
    protected $date;
    /**
     * @Key("boolean")
     * @Type("boolean")
     */
    protected $boolean;
    /**
     * @Key("string")
     * @Type("string")
     */
    protected $string;
    /**
     * @param string $foo
     */
    public function setFoo(?string $foo)
    {
        $this->foo = $foo;
    }
    /**
     * @return string
     */
    public function getFoo() : ?string
    {
        return $this->foo;
    }
    /**
     * @param string $bar
     */
    public function setBar(?string $bar)
    {
        $this->bar = $bar;
    }
    /**
     * @return string
     */
    public function getBar() : ?string
    {
        return $this->bar;
    }
    /**
     * @param string $baz
     */
    public function setBaz(?string $baz)
    {
        $this->baz = $baz;
    }
    /**
     * @return string
     */
    public function getBaz() : ?string
    {
        return $this->baz;
    }
    /**
     * @param string $boz
     */
    public function setBoz(?string $boz)
    {
        $this->boz = $boz;
    }
    /**
     * @return string
     */
    public function getBoz() : ?string
    {
        return $this->boz;
    }
    /**
     * @param int $integer
     */
    public function setInteger(?int $integer)
    {
        $this->integer = $integer;
    }
    /**
     * @return int
     */
    public function getInteger() : ?int
    {
        return $this->integer;
    }
    /**
     * @param float $number
     */
    public function setNumber(?float $number)
    {
        $this->number = $number;
    }
    /**
     * @return float
     */
    public function getNumber() : ?float
    {
        return $this->number;
    }
    /**
     * @param string $date
     */
    public function setDate(?string $date)
    {
        $this->date = $date;
    }
    /**
     * @return string
     */
    public function getDate() : ?string
    {
        return $this->date;
    }
    /**
     * @param bool $boolean
     */
    public function setBoolean(?bool $boolean)
    {
        $this->boolean = $boolean;
    }
    /**
     * @return bool
     */
    public function getBoolean() : ?bool
    {
        return $this->boolean;
    }
    /**
     * @param string $string
     */
    public function setString(?string $string)
    {
        $this->string = $string;
    }
    /**
     * @return string
     */
    public function getString() : ?string
    {
        return $this->string;
    }
}
/**
 * @Title("Endpoint")
 */
class Endpoint
{
    /**
     * @Key("GetQuery")
     * @Ref("Foo\GetQuery")
     */
    protected $GetQuery;
    /**
     * @Key("Song")
     * @Ref("Foo\Song")
     */
    protected $Song;
    /**
     * @param GetQuery $GetQuery
     */
    public function setGetQuery(?GetQuery $GetQuery)
    {
        $this->GetQuery = $GetQuery;
    }
    /**
     * @return GetQuery
     */
    public function getGetQuery() : ?GetQuery
    {
        return $this->GetQuery;
    }
    /**
     * @param Song $Song
     */
    public function setSong(?Song $Song)
    {
        $this->Song = $Song;
    }
    /**
     * @return Song
     */
    public function getSong() : ?Song
    {
        return $this->Song;
    }
}

