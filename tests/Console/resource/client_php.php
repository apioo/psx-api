<?php

namespace Foo;

use GuzzleHttp\Client;
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
    public function setAuthor(?string $author)
    {
        $this->author = $author;
    }
    public function getAuthor() : ?string
    {
        return $this->author;
    }
    public function setRating(?int $rating)
    {
        $this->rating = $rating;
    }
    public function getRating() : ?int
    {
        return $this->rating;
    }
    public function setText(?string $text)
    {
        $this->text = $text;
    }
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
     * @Items(@Ref("PSX\Generation\Rating"))
     */
    protected $ratings;
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }
    public function getTitle() : ?string
    {
        return $this->title;
    }
    public function setArtist(?string $artist)
    {
        $this->artist = $artist;
    }
    public function getArtist() : ?string
    {
        return $this->artist;
    }
    public function setLength(?int $length)
    {
        $this->length = $length;
    }
    public function getLength() : ?int
    {
        return $this->length;
    }
    public function setRatings(?array $ratings)
    {
        $this->ratings = $ratings;
    }
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
    public function setFoo(?string $foo)
    {
        $this->foo = $foo;
    }
    public function getFoo() : ?string
    {
        return $this->foo;
    }
    public function setBar(?string $bar)
    {
        $this->bar = $bar;
    }
    public function getBar() : ?string
    {
        return $this->bar;
    }
    public function setBaz(?string $baz)
    {
        $this->baz = $baz;
    }
    public function getBaz() : ?string
    {
        return $this->baz;
    }
    public function setBoz(?string $boz)
    {
        $this->boz = $boz;
    }
    public function getBoz() : ?string
    {
        return $this->boz;
    }
    public function setInteger(?int $integer)
    {
        $this->integer = $integer;
    }
    public function getInteger() : ?int
    {
        return $this->integer;
    }
    public function setNumber(?float $number)
    {
        $this->number = $number;
    }
    public function getNumber() : ?float
    {
        return $this->number;
    }
    public function setDate(?string $date)
    {
        $this->date = $date;
    }
    public function getDate() : ?string
    {
        return $this->date;
    }
    public function setBoolean(?bool $boolean)
    {
        $this->boolean = $boolean;
    }
    public function getBoolean() : ?bool
    {
        return $this->boolean;
    }
    public function setString(?string $string)
    {
        $this->string = $string;
    }
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
     * @Ref("PSX\Generation\GetQuery")
     */
    protected $GetQuery;
    /**
     * @Key("Song")
     * @Ref("PSX\Generation\Song")
     */
    protected $Song;
    public function setGetQuery(?GetQuery $GetQuery)
    {
        $this->GetQuery = $GetQuery;
    }
    public function getGetQuery() : ?GetQuery
    {
        return $this->GetQuery;
    }
    public function setSong(?Song $Song)
    {
        $this->Song = $Song;
    }
    public function getSong() : ?Song
    {
        return $this->Song;
    }
}

