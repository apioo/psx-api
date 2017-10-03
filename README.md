PSX Api
===

## About

Currently there are several API specification formats (RAML, Swagger, etc.)
which describe the functionality of an API endpoint. This project provides a
data model which contains the common information of an API endpoint. There are
parser classes which create such models based on a specification and it is also
possible to generate a specification from a model object. We have also created 
an [online tool](http://phpsx.org/tools/openapi) to test those conversions.

### Parser

- Annotation (Parses a class which contains annotations)
- OpenAPI ([OpenAPI 3.0](https://www.openapis.org/) specification)
- RAML ([RAML 0.8/1.0](http://raml.org/) specification)
- Swagger ([Swagger 2.0](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/2.0.md) specification)

### Generator

- HTML (Generates a HTML representation of the resource)
- JsonSchema (Generates a [JsonSchema](http://json-schema.org/) which contains all schemas of the specification)
- Markdown (Generates a Markdown representation of the resource)
- OpenAPI (Generates a [OpenAPI 3.0](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md) specification)
- PHP (Generates PHP source code which represents a controller consuming the API resource)
- RAML (Generates a [RAML 1.0](http://raml.org/) specification)
- Swagger (Generates a [Swagger 2.0](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/2.0.md) specification)
- Template (Generates a representation based on a [Twig](https://twig.symfony.com/) template)

## Usage

The root model object is called `resource` which represents a specific API
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

// creates a PHP controller which consumes the defined /foo resource
$generator = new \PSX\Api\Generator\Php();

$source = $generator->generate($resource);

```
