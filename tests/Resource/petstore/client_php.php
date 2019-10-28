<?php 
/**
 * PetsResource generated on 0000-00-00
 * @see https://github.com/apioo
 */


use GuzzleHttp\Client;
use PSX\Json\Parser;
use PSX\Record\RecordInterface;
use PSX\Schema\Parser\Popo\Dumper;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaTraverser;
use PSX\Schema\Visitor\TypeVisitor;

class PetsResource
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
     *
     * @param GetQuery $query
     * @return Pets
     */
    public function listPets(?GetQuery $query): Pets
    {
        $options = [
            'query' => $this->prepare($query, true),
        ];

        $response = $this->httpClient->request('GET', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, Pets::class);
    }

    /**
     * Create a pet
     *
     * @param Pet $data
     * @return void
     */
    public function createPets(?Pet $data)
    {
        $options = [
            'json' => $this->prepare($data)
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, null);
    }

    private function prepare($object, bool $asArray = false)
    {
        $data = (new Dumper())->dump($object);
        if ($asArray) {
            if ($data instanceof RecordInterface) {
                return $data->getProperties();
            } else {
                return [];
            }
        } else {
            return $data;
        }
    }

    private function parse(string $data, ?string $class)
    {
        $data = Parser::decode($data);
        if ($class !== null) {
            $schema = $this->schemaManager->getSchema($class);
            return (new SchemaTraverser(false))->traverse($data, $schema, new TypeVisitor());
        } else {
            return $data;
        }
    }
}

<?php 
/**
 * PetsResourceSchema generated on 0000-00-00
 * @see https://github.com/apioo
 */

/**
 * @Title("PetsResourceSchema")
 */
class PetsResourceSchema
{
    /**
     * @Key("GetQuery")
     * @Ref("\GetQuery")
     */
    protected $GetQuery;
    /**
     * @Key("Pets")
     * @Ref("\Pets")
     */
    protected $Pets;
    /**
     * @Key("Pet")
     * @Ref("\Pet")
     */
    protected $Pet;
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
     * @param Pets $Pets
     */
    public function setPets(?Pets $Pets)
    {
        $this->Pets = $Pets;
    }
    /**
     * @return Pets
     */
    public function getPets() : ?Pets
    {
        return $this->Pets;
    }
    /**
     * @param Pet $Pet
     */
    public function setPet(?Pet $Pet)
    {
        $this->Pet = $Pet;
    }
    /**
     * @return Pet
     */
    public function getPet() : ?Pet
    {
        return $this->Pet;
    }
}
<?php 
/**
 * GetQuery generated on 0000-00-00
 * @see https://github.com/apioo
 */

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
    /**
     * @param int $limit
     */
    public function setLimit(?int $limit)
    {
        $this->limit = $limit;
    }
    /**
     * @return int
     */
    public function getLimit() : ?int
    {
        return $this->limit;
    }
}
<?php 
/**
 * Pets generated on 0000-00-00
 * @see https://github.com/apioo
 */

/**
 * @Title("Pets")
 */
class Pets
{
    /**
     * @Key("pets")
     * @Type("array")
     * @Items(@Ref("\Pet"))
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
/**
 * Pet generated on 0000-00-00
 * @see https://github.com/apioo
 */

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