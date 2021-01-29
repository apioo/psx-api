PSX Api
===

## About

This library provides model classes to describe your API. Based on those models
it is possible to generate i.e. API specification formats like OpenAPI and
also client code which can be used at an SDK. It is also possible to generate
those model classes either via annotations or from an OpenAPI specification.
We have also created an [online tool](http://phpsx.org/tools/openapi) to test
those conversions.

### Generator

#### Client

- Go (in development)
- Java (in development)
- PHP (stable)
- Typescript (stable)

#### Markup

- HTML
- Markdown

#### Spec

- OpenAPI (Generates a [OpenAPI 3.0](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md) specification)
- RAML (Generates a [RAML 1.0](http://raml.org/) specification)
- TypeSchema (Generates a [TypeSchema](https://typeschema.org/) specification)

## Usage

The root model object is called `Resource` which represents a specific API
endpoint. The following is a simple showcase of the resource API so you get a
basic understanding how it is designed.

```php
<?php

// reads the OpenAPI specification and generates a resource object which was
// defined under the path /foo
$resource = \PSX\Api\Parser\OpenAPI::fromFile('openapi.json', '/foo');

// returns the title
$resource->getTitle();

// returns available path parameters as PSX\Schema\PropertyInterface
$resource->getPathParameters();

// checks whether a specific request method is supported
$resource->hasMethod('POST');

// returns all allowed methods
$resource->getAllowedMethods();

// returns the available query parameters per method as PSX\Schema\PropertyInterface
$resource->getMethod('POST')->getQueryParameters();

// checks whether the method has a request specification
$resource->getMethod('POST')->hasRequest();

// returns the request body specification as PSX\Schema\SchemaInterface
$resource->getMethod('POST')->getRequest();

// checks whether the method has a response with the status code 201
$resource->getMethod('POST')->hasResponse(201);

// returns the response body specification as PSX\Schema\SchemaInterface
$resource->getMethod('POST')->getResponse(201);

// creates a PHP client which consumes the defined /foo resource
$generator = new \PSX\Api\Generator\Client\Php();

$source = $generator->generate($resource);

```
