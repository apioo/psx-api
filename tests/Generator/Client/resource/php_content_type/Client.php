<?php
/**
 * Client automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use GuzzleHttp\Exception\BadResponseException;
use Sdkgen\Client\ClientAbstract;
use Sdkgen\Client\Credentials;
use Sdkgen\Client\CredentialsInterface;
use Sdkgen\Client\Exception\ClientException;
use Sdkgen\Client\Exception\Payload;
use Sdkgen\Client\Exception\UnknownStatusCodeException;
use Sdkgen\Client\TokenStoreInterface;

class Client extends ClientAbstract
{
    /**
     * @param \Psr\Http\Message\StreamInterface $body
     * @return \Psr\Http\Message\StreamInterface
     * @throws BinaryException
     * @throws ClientException
     */
    public function binary(\Psr\Http\Message\StreamInterface $body): \Psr\Http\Message\StreamInterface
    {
        $url = $this->parser->url('/binary', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/octet-stream',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'body' => $body
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = $body;

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                case $statusCode >= 0 && $statusCode <= 999:
                    $data = $body;

                    throw new BinaryException($data);
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param array $body
     * @return array
     * @throws FormException
     * @throws ClientException
     */
    public function form(array $body): array
    {
        $url = $this->parser->url('/form', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'form_params' => $body
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = [];
            parse_str((string) $body, $data);

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                case $statusCode >= 500 && $statusCode <= 599:
                    $data = [];
                    parse_str((string) $body, $data);

                    throw new FormException($data);
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param \stdClass $body
     * @return \stdClass
     * @throws JsonException
     * @throws ClientException
     */
    public function json(\stdClass $body): \stdClass
    {
        $url = $this->parser->url('/json', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'json' => $body
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = \json_decode((string) $body);

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                case $statusCode >= 400 && $statusCode <= 499:
                    $data = \json_decode((string) $body);

                    throw new JsonException($data);
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param array $body
     * @return array
     * @throws MultipartException
     * @throws ClientException
     */
    public function multipart(array $body): array
    {
        $url = $this->parser->url('/multipart', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'multipart' => $body
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            // @TODO currently not possible, please create an issue at https://github.com/apioo/psx-api if needed
            $data = [];

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                case $statusCode === 500:
                    // @TODO currently not possible, please create an issue at https://github.com/apioo/psx-api if needed
                    $data = [];

                    throw new MultipartException($data);
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param string $body
     * @return string
     * @throws TextException
     * @throws ClientException
     */
    public function text(string $body): string
    {
        $url = $this->parser->url('/text', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'text/plain',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'body' => $body
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = (string) $body;

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                case $statusCode === 500:
                    $data = (string) $body;

                    throw new TextException($data);
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param \DOMDocument $body
     * @return \DOMDocument
     * @throws XmlException
     * @throws ClientException
     */
    public function xml(\DOMDocument $body): \DOMDocument
    {
        $url = $this->parser->url('/xml', [
        ]);

        $options = [
            'headers' => [
                'Content-Type' => 'application/xml',
            ],
            'query' => $this->parser->query([
            ], [
            ]),
            'body' => $body->saveXML()
        ];

        try {
            $response = $this->httpClient->request('POST', $url, $options);
            $body = $response->getBody();

            $data = new \DOMDocument();
            $data->loadXML((string) $body);

            return $data;
        } catch (ClientException $e) {
            throw $e;
        } catch (BadResponseException $e) {
            $body = $e->getResponse()->getBody();
            $statusCode = $e->getResponse()->getStatusCode();

            switch (true) {
                case $statusCode === 500:
                    $data = new \DOMDocument();
                    $data->loadXML((string) $body);

                    throw new XmlException($data);
                default:
                    throw new UnknownStatusCodeException('The server returned an unknown status code');
            }
        } catch (\Throwable $e) {
            throw new ClientException('An unknown error occurred: ' . $e->getMessage());
        }
    }





    public static function buildAnonymous(): self
    {
        return new self('http://api.foo.com', new Credentials\Anonymous());
    }
}
