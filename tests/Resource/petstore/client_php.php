<?php

namespace Pets;

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

    public function __construct(string $baseUrl, string $token, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {

        $this->url = $baseUrl . '/pets';
        $this->token = $token;
        $this->httpClient = $httpClient ? $httpClient : new Client();
        $this->schemaManager = $schemaManager ? $schemaManager : new SchemaManager();
    }

    /**
     * List all pets
     */
    public function listPets(GetQuery $query): Pets
    {
        $options = [
            'query' => $this->convertToArray($query),
        ];

        $response = $this->httpClient->request('GET', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->convertToObject($data, Pets::class);
    }

    /**
     * Create a pet
     */
    public function createPets(Pet $data)
    {
        $options = [
            'json' => $this->convertToArray($data)
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->convertToObject($data, null);
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
/**
 * @Title("GetQuery")
 */
class GetQuery
{
    /**
     * @Key("limit")
     * @Type("integer")
     * @Format("int32")
     */
    protected $limit;
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    public function getLimit()
    {
        return $this->limit;
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
     * @Key("Pets")
     * @Ref("PSX\Generation\Pets")
     */
    protected $Pets;
    /**
     * @Key("Pet")
     * @Ref("PSX\Generation\Pet")
     */
    protected $Pet;
    public function setGetQuery($GetQuery)
    {
        $this->GetQuery = $GetQuery;
    }
    public function getGetQuery()
    {
        return $this->GetQuery;
    }
    public function setPets($Pets)
    {
        $this->Pets = $Pets;
    }
    public function getPets()
    {
        return $this->Pets;
    }
    public function setPet($Pet)
    {
        $this->Pet = $Pet;
    }
    public function getPet()
    {
        return $this->Pet;
    }
}

