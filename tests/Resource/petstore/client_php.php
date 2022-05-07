<?php
/**
 * PetsResource generated on 0000-00-00
 * @see https://sdkgen.app
 */


use GuzzleHttp\Client;
use PSX\Http\Exception\StatusCodeException;
use PSX\Schema\SchemaManager;
use Sdkgen\Client\ResourceAbstract;

class PetsResource extends ResourceAbstract
{
    private string $url;


    public function __construct(string $baseUrl, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {
        parent::__construct($baseUrl, $httpClient, $schemaManager);

        $this->url = $this->baseUrl . '/pets';
    }

    /**
     * List all pets
     *
     * @param PetsGetQuery|null $query
     * @return Pets
     * @throws \PSX\Http\Exception\StatusCodeException
     */
    public function listPets(?PetsGetQuery $query = null): Pets
    {
        $options = [
            'query' => $query !== null ? (array) $query->jsonSerialize() : [],
        ];

        $response = $this->httpClient->request('GET', $this->url, $options);
        $data     = (string) $response->getBody();

        if ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
            StatusCodeException::throwOnRedirection($response);
        } elseif ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            StatusCodeException::throwOnClientError($response);
        } elseif ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            StatusCodeException::throwOnServerError($response);
        }

        return $this->parse($data, Pets::class);
    }

    /**
     * Create a pet
     *
     * @param Pet $data
     * @return void
     * @throws \PSX\Http\Exception\StatusCodeException
     */
    public function createPets(Pet $data): void
    {
        $options = [
            'json' => $data
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        if ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
            StatusCodeException::throwOnRedirection($response);
        } elseif ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            StatusCodeException::throwOnClientError($response);
        } elseif ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            StatusCodeException::throwOnServerError($response);
        }

    }

}

<?php
/**
 * Pet generated on 0000-00-00
 * @see https://sdkgen.app
 */

use PSX\Schema\Attribute\Required;
use PSX\Schema\Attribute\Title;

#[Title('Pet')]
#[Required(array('id', 'name'))]
class Pet implements \JsonSerializable
{
    protected ?int $id = null;
    protected ?string $name = null;
    protected ?string $tag = null;
    public function setId(?int $id) : void
    {
        $this->id = $id;
    }
    public function getId() : ?int
    {
        return $this->id;
    }
    public function setName(?string $name) : void
    {
        $this->name = $name;
    }
    public function getName() : ?string
    {
        return $this->name;
    }
    public function setTag(?string $tag) : void
    {
        $this->tag = $tag;
    }
    public function getTag() : ?string
    {
        return $this->tag;
    }
    public function jsonSerialize() : \stdClass
    {
        return (object) array_filter(array('id' => $this->id, 'name' => $this->name, 'tag' => $this->tag), static function ($value) : bool {
            return $value !== null;
        });
    }
}

<?php
/**
 * Pets generated on 0000-00-00
 * @see https://sdkgen.app
 */

use PSX\Schema\Attribute\Title;

#[Title('Pets')]
class Pets implements \JsonSerializable
{
    /**
     * @var array<Pet>|null
     */
    protected ?array $pets = null;
    /**
     * @param array<Pet>|null $pets
     */
    public function setPets(?array $pets) : void
    {
        $this->pets = $pets;
    }
    public function getPets() : ?array
    {
        return $this->pets;
    }
    public function jsonSerialize() : \stdClass
    {
        return (object) array_filter(array('pets' => $this->pets), static function ($value) : bool {
            return $value !== null;
        });
    }
}

<?php
/**
 * Error generated on 0000-00-00
 * @see https://sdkgen.app
 */

use PSX\Schema\Attribute\Required;
use PSX\Schema\Attribute\Title;

#[Title('Error')]
#[Required(array('code', 'message'))]
class Error implements \JsonSerializable
{
    protected ?int $code = null;
    protected ?string $message = null;
    public function setCode(?int $code) : void
    {
        $this->code = $code;
    }
    public function getCode() : ?int
    {
        return $this->code;
    }
    public function setMessage(?string $message) : void
    {
        $this->message = $message;
    }
    public function getMessage() : ?string
    {
        return $this->message;
    }
    public function jsonSerialize() : \stdClass
    {
        return (object) array_filter(array('code' => $this->code, 'message' => $this->message), static function ($value) : bool {
            return $value !== null;
        });
    }
}

<?php
/**
 * PetsGetQuery generated on 0000-00-00
 * @see https://sdkgen.app
 */

use PSX\Schema\Attribute\Required;

#[Required(array())]
class PetsGetQuery implements \JsonSerializable
{
    protected ?int $limit = null;
    public function setLimit(?int $limit) : void
    {
        $this->limit = $limit;
    }
    public function getLimit() : ?int
    {
        return $this->limit;
    }
    public function jsonSerialize() : \stdClass
    {
        return (object) array_filter(array('limit' => $this->limit), static function ($value) : bool {
            return $value !== null;
        });
    }
}

<?php
/**
 * PetsPetIdGetQuery generated on 0000-00-00
 * @see https://sdkgen.app
 */

use PSX\Schema\Attribute\Required;

#[Required(array())]
class PetsPetIdGetQuery implements \JsonSerializable
{
    protected ?string $petId = null;
    public function setPetId(?string $petId) : void
    {
        $this->petId = $petId;
    }
    public function getPetId() : ?string
    {
        return $this->petId;
    }
    public function jsonSerialize() : \stdClass
    {
        return (object) array_filter(array('petId' => $this->petId), static function ($value) : bool {
            return $value !== null;
        });
    }
}

<?php
/**
 * Client generated on 0000-00-00
 * @see https://sdkgen.app
 */


use Sdkgen\Client\ClientAbstract;
use Sdkgen\Client\Credentials;
use Sdkgen\Client\CredentialsInterface;
use Sdkgen\Client\TokenStoreInterface;

class Client extends ClientAbstract
{
    public function __construct(string $baseUrl, ?CredentialsInterface $credentials = null, ?TokenStoreInterface $tokenStore = null, ?array $scopes = null)
    {
        parent::__construct($baseUrl, $credentials, $tokenStore, $scopes);
    }

    /**
     * Endpoint: /pets
     */
    public function getPets(): PetsResource
    {
        return new PetsResource(
            $this->baseUrl,
            $this->newHttpClient(),
            $this->schemaManager
        );
    }

}
