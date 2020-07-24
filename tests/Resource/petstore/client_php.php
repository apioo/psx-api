<?php 
/**
 * PetsResource generated on 0000-00-00
 * @see https://github.com/apioo
 */


use GuzzleHttp\Client;
use PSX\Api\Generator\Client\Php\ResourceAbstract;
use PSX\Schema\SchemaManager;

class PetsResource extends ResourceAbstract
{
    /**
     * @var string
     */
    private $url;

    public function __construct(string $baseUrl, string $token, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {
        parent::__construct($baseUrl, $token, $httpClient, $schemaManager);

        $this->url = $this->baseUrl . '/pets';
    }

    /**
     * List all pets
     *
     * @param PetsGetQuery $query
     * @return Pets
     */
    public function listPets(?PetsGetQuery $query): Pets
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
class Pet implements \JsonSerializable
{
    /**
     * @var int|null
     */
    protected $id;
    /**
     * @var string|null
     */
    protected $name;
    /**
     * @var string|null
     */
    protected $tag;
    /**
     * @param int|null $id
     */
    public function setId(?int $id) : void
    {
        $this->id = $id;
    }
    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }
    /**
     * @param string|null $name
     */
    public function setName(?string $name) : void
    {
        $this->name = $name;
    }
    /**
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    /**
     * @param string|null $tag
     */
    public function setTag(?string $tag) : void
    {
        $this->tag = $tag;
    }
    /**
     * @return string|null
     */
    public function getTag() : ?string
    {
        return $this->tag;
    }
    public function jsonSerialize()
    {
        return array_filter(array('id' => $this->id, 'name' => $this->name, 'tag' => $this->tag), static function ($value) : bool {
            return $value !== null;
        });
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
class Pets implements \JsonSerializable
{
    /**
     * @var array<Pet>|null
     */
    protected $pets;
    /**
     * @param array<Pet>|null $pets
     */
    public function setPets(?array $pets) : void
    {
        $this->pets = $pets;
    }
    /**
     * @return array<Pet>|null
     */
    public function getPets() : ?array
    {
        return $this->pets;
    }
    public function jsonSerialize()
    {
        return array_filter(array('pets' => $this->pets), static function ($value) : bool {
            return $value !== null;
        });
    }
}
<?php 
/**
 * Error generated on 0000-00-00
 * @see https://github.com/apioo
 */

/**
 * @Title("Error")
 * @Required({"code", "message"})
 */
class Error implements \JsonSerializable
{
    /**
     * @var int|null
     */
    protected $code;
    /**
     * @var string|null
     */
    protected $message;
    /**
     * @param int|null $code
     */
    public function setCode(?int $code) : void
    {
        $this->code = $code;
    }
    /**
     * @return int|null
     */
    public function getCode() : ?int
    {
        return $this->code;
    }
    /**
     * @param string|null $message
     */
    public function setMessage(?string $message) : void
    {
        $this->message = $message;
    }
    /**
     * @return string|null
     */
    public function getMessage() : ?string
    {
        return $this->message;
    }
    public function jsonSerialize()
    {
        return array_filter(array('code' => $this->code, 'message' => $this->message), static function ($value) : bool {
            return $value !== null;
        });
    }
}
<?php 
/**
 * PetsGetQuery generated on 0000-00-00
 * @see https://github.com/apioo
 */

/**
 * @Required({})
 */
class PetsGetQuery implements \JsonSerializable
{
    /**
     * @var int|null
     */
    protected $limit;
    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit) : void
    {
        $this->limit = $limit;
    }
    /**
     * @return int|null
     */
    public function getLimit() : ?int
    {
        return $this->limit;
    }
    public function jsonSerialize()
    {
        return array_filter(array('limit' => $this->limit), static function ($value) : bool {
            return $value !== null;
        });
    }
}
<?php 
/**
 * PetsPetIdGetQuery generated on 0000-00-00
 * @see https://github.com/apioo
 */

/**
 * @Required({})
 */
class PetsPetIdGetQuery implements \JsonSerializable
{
    /**
     * @var string|null
     */
    protected $petId;
    /**
     * @param string|null $petId
     */
    public function setPetId(?string $petId) : void
    {
        $this->petId = $petId;
    }
    /**
     * @return string|null
     */
    public function getPetId() : ?string
    {
        return $this->petId;
    }
    public function jsonSerialize()
    {
        return array_filter(array('petId' => $this->petId), static function ($value) : bool {
            return $value !== null;
        });
    }
}