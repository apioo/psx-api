
# API

The API component provides models to describe an API specification. You can create those models either by parsing an
OpenAPI specification or by using PHP Attributes. Based on those models it is then possible to generate i.e. an OpenAPI
specification or client SDKs.

## Usage

The root model object is called a `Specification` which then contains a resources and definitions. The resources contain
all available metadata of your endpoints and the definitions represent the available schemas.

### Framework

If you want to integrate this library into your framework you can implement a `ListingInterface`. A listing basically
knows every API endpoint of your framework and returns a `SpecificationInterface`. The listing is then also used at
our commands to generate an OpenAPI specification or the client SDK.

You can use PHP attributes to describe the structure of your endpoints. The parser looks at the provided class and
builds a specification based on the provided attributes. Most likely you want to add those attributes to your controller
class. A controller class could then look like:

```php
<?php

class MyController
{
    #[Get]
    #[Path('/my/endpoint')]
    #[QueryParam(name='foo', type='integer')]
    #[Outgoing(code=200, schema=\My\Response\Model::class)]
    public function getModel()
    {
        // @TODO implement
    }
    
    #[Post]
    #[Path('/my/endpoint')]
    #[Incoming(schema=\My\Request\Model::class)]
    #[Outgoing(code=200, schema=\My\Response\Model::class)]
    public function insertModel(\My\Request\Model $model)
    {
        // @TODO implement
    }
}

```

This would be then enough for the API component to generate either an OpenAPI specification or a client SDK.
Note this library only needs the meta information, if you can get those meta information at your framework in another
way you can also implement a custom `ParserInterface`.

### Standalone

Beside the framework integration you can use this component also to simply parse existing OpenAPI specifications and
generate specific output. The following is a simple example how to use the PHP API and how to generate code.

```php
<?php

// reads the OpenAPI specification and generates a resource object which was defined under the path /foo
$specification = \PSX\Api\Parser\OpenAPI::fromFile('openapi.json');

// contains all schema type definitions
$definitions = $specification->getDefinitions();

// returns the resource foo from the specification
$resource = $specification->get('/foo');

// returns path parameters type as string
$resource->getPathParameters();

// you can get the actual schema type from the definitions
$pathType = $definitions->getType($resource->getPathParameters());

// checks whether a specific request method is supported
$resource->hasMethod('POST');

// returns all allowed methods
$resource->getAllowedMethods();

// returns the available query parameters type as string
$resource->getMethod('POST')->getQueryParameters();

// you can get the actual schema type from the definitions
$queryType = $definitions->getType($resource->getMethod('POST')->getQueryParameters());

// checks whether the method has a request
$resource->getMethod('POST')->hasRequest();

// returns the request type as string
$resource->getMethod('POST')->getRequest();

// you can get the actual schema type from the definitions
$requestType = $definitions->getType($resource->getMethod('POST')->getRequest());

// checks whether the method has a response with the status code 201
$resource->getMethod('POST')->hasResponse(201);

// returns the response type as string
$resource->getMethod('POST')->getResponse(201);

// you can get the actual schema type from the definitions
$responseType = $definitions->getType($resource->getMethod('POST')->getResponse(201));

// creates a PHP client which consumes the defined /foo resource
$generator = new \PSX\Api\Generator\Client\Php();

$source = $generator->generate($resource);

```

## Generator

### Client

- PHP (stable)
- Typescript (stable)
- Go (in development)
- Java (in development)

### Markup

- HTML
- Markdown

### Spec

- OpenAPI (Generates a [OpenAPI 3.0](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md) specification)
- RAML (Generates a [RAML 1.0](http://raml.org/) specification)
- TypeSchema (Generates a [TypeSchema](https://typeschema.org/) specification)
