<?php
/**
 * Client automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */

namespace Foo\Bar;

use GuzzleHttp\Exception\BadResponseException;
use Sdkgen\Client\ClientAbstract;
use Sdkgen\Client\Credentials;
use Sdkgen\Client\Exception\ClientException;
use Sdkgen\Client\Exception\UnknownStatusCodeException;

class Client extends ClientAbstract
{
    public function foo(): FooTag
    {
        return new FooTag(
            $this->httpClient,
            $this->parser
        );
    }

    public function bar(): BarTag
    {
        return new BarTag(
            $this->httpClient,
            $this->parser
        );
    }

    public function baz(): BazTag
    {
        return new BazTag(
            $this->httpClient,
            $this->parser
        );
    }



    public static function build(string $token): self
    {
        return new self('http://api.foo.com', new Credentials\HttpBearer($token));
    }
}
