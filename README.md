
# API

The API component is the reference implementation of the [TypeAPI](http://typeapi.org/) specification.
It provides models to describe an REST API and generate based on those models different outputs. You can create those
models either by parsing a TypeAPI or OpenAPI specification or by using PHP Attributes. Based on those models it is then
possible to generate i.e. client SDKs.

## Usage

The root model object is called a `Specification` which then contains `Operations` and `Definitions`. The operations
contain all available metadata of your endpoints and the definitions represent the available schemas.

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
    public function getModel(QueryParam $param, ): \My\Response\Model
    {
        // @TODO implement
    }
    
    #[Post]
    #[Path('/my/endpoint')]
    public function insertModel(\My\Request\Model $model): \My\Response\Model
    {
        // @TODO implement
    }
}

```

This would be then enough for the API component to generate either an OpenAPI specification or a client SDK.
Note this library only needs the meta information, if you can get those meta information at your framework in another
way you can also implement a custom `ParserInterface`.

### Standalone

Beside the framework integration you can use this component also to simply parse existing TypeAPI specification and
generate specific output. The following is a simple example how to use the PHP API and how to generate code.

```php
<?php

// reads the TypeAPI specification and generates a specification
$specification = \PSX\Api\Parser\TypeAPI::fromFile('typeapi.json');

// contains all schema type definitions
$definitions = $specification->getDefinitions();

// returns the resource foo from the specification
$operation = $specification->getOperations()->get('my.operation');

// returns all available arguments
$operation->getArguments();

// returns the return type
$operation->getReturn();

// returns all exceptions which are described
$operation->getThrows();

// returns the assigned HTTP method
$operation->getMethod();

// returns the assigned HTTP path
$operation->getPath();

// creates a PHP client which consumes the defined operations
$generator = new \PSX\Api\Generator\Client\Php();

$source = $generator->generate($resource);

```

## Generator

### Client

- Go
- Java
- PHP
- Typescript

### Markup

- HTML
- Markdown

### Spec

- [OpenAPI 3.0](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md)
- [RAML 1.0](http://raml.org/)
- [TypeAPI](https://typeapi.org/)
- [TypeSchema](https://typeschema.org/)
