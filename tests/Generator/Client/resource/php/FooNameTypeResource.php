<?php 
/**
 * FooNameTypeResource generated on 0000-00-00
 * @see https://github.com/apioo
 */


use GuzzleHttp\Client;
use PSX\Json\Parser;
use PSX\Record\RecordInterface;
use PSX\Schema\Parser\Popo\Dumper;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaTraverser;
use PSX\Schema\Visitor\TypeVisitor;

class FooNameTypeResource
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
    private $name;

    /**
     * @var string
     */
    private $type;

    public function __construct(string $name, string $type, string $baseUrl, string $token, ?Client $httpClient = null, ?SchemaManager $schemaManager = null)
    {
        $this->name = $name;
        $this->type = $type;

        $this->url = $baseUrl . '/foo/' . $name . '/' . $type . '';
        $this->token = $token;
        $this->httpClient = $httpClient ? $httpClient : new Client();
        $this->schemaManager = $schemaManager ? $schemaManager : new SchemaManager();
    }

    /**
     * Returns a collection
     *
     * @param GetQuery $query
     * @return Collection
     */
    public function listFoo(?GetQuery $query): Collection
    {
        $options = [
            'query' => $this->prepare($query, true),
        ];

        $response = $this->httpClient->request('GET', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, Collection::class);
    }

    /**
     * @param ItemCreate $data
     * @return Message
     */
    public function createFoo(?ItemCreate $data): Message
    {
        $options = [
            'json' => $this->prepare($data)
        ];

        $response = $this->httpClient->request('POST', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, Message::class);
    }

    /**
     * @param ItemUpdate $data
     * @return Message
     */
    public function put(?ItemUpdate $data): Message
    {
        $options = [
            'json' => $this->prepare($data)
        ];

        $response = $this->httpClient->request('PUT', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, Message::class);
    }

    /**
     * @return Message
     */
    public function delete(): Message
    {
        $options = [
        ];

        $response = $this->httpClient->request('DELETE', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, Message::class);
    }

    /**
     * @param ItemPatch $data
     * @return Message
     */
    public function patch(?ItemPatch $data): Message
    {
        $options = [
            'json' => $this->prepare($data)
        ];

        $response = $this->httpClient->request('PATCH', $this->url, $options);
        $data     = (string) $response->getBody();

        return $this->parse($data, Message::class);
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
