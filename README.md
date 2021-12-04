PSX Api
===

## About

This library provides model classes to describe an API specification. Based on those
models it is possible to generate i.e. API specification formats like OpenAPI and
also client code which can be used at an SDK. It is also possible to generate
those model classes either via attributes or from an OpenAPI specification.

### Generator

#### Client

- PHP (stable)
- Typescript (stable)
- Go (in development)
- Java (in development)

#### Markup

- HTML
- Markdown

#### Spec

- OpenAPI (Generates a [OpenAPI 3.0](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md) specification)
- RAML (Generates a [RAML 1.0](http://raml.org/) specification)
- TypeSchema (Generates a [TypeSchema](https://typeschema.org/) specification)

## Usage

The root model object is called `Specification` which represents an API specification.
In the following an example how you can use the API:

```php
<?php

// reads the OpenAPI specification and generates a resource object which was
// defined under the path /foo
$specification = \PSX\Api\Parser\OpenAPI::fromFile('openapi.json');

// contains all schema type definitions
$definitions = $specification->getDefinitions();

// returns the resource foo from the specification
$resource = $specification->get('/foo');

// returns path parameters type as string
$resource->getPathParameters();

// checks whether a specific request method is supported
$resource->hasMethod('POST');

// returns all allowed methods
$resource->getAllowedMethods();

// returns the available query parameters type as string
$resource->getMethod('POST')->getQueryParameters();

// checks whether the method has a request specification
$resource->getMethod('POST')->hasRequest();

// returns the request type as string
$resource->getMethod('POST')->getRequest();

// checks whether the method has a response with the status code 201
$resource->getMethod('POST')->hasResponse(201);

// returns the response type as string
$resource->getMethod('POST')->getResponse(201);

// creates a PHP client which consumes the defined /foo resource
$generator = new \PSX\Api\Generator\Client\Php();

$source = $generator->generate($resource);

```
