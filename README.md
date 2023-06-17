
# API

The API component is the reference implementation of the [TypeAPI](https://typeapi.org/) specification.
It provides models to describe an REST API and generate based on those models different outputs. You can create those
models either by parsing a TypeAPI or OpenAPI specification or by using PHP Attributes. Based on those models it is then
possible to generate i.e. client SDKs.

## Usage

The root model object is called a `Specification` which contains `Operations` and `Definitions`. Each operation
maps to a specific REST API endpoint and the definitions represent the schemas to describe the JSON request or response
payload.

### Framework

You can use PHP attributes to describe the structure of your endpoints. You can then use the attribute parser (`PSX\Api\Parser\Attribute`)
to automatically generate a specification for your controller. A controller class could then look like:

```php
<?php

class MyController
{
    #[Get]
    #[Path('/my/endpoint/:id')]
    public function getModel(int $id, int $year): \My\Response\Model
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

// use the API manager to obtain a specification from different sources
$manager = new \PSX\Api\ApiManager(new \PSX\Schema\SchemaManager());

// reads the TypeAPI specification and generates a specification
$specification = $manager->getApi('./typeapi.json');

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
$registry = \PSX\Api\GeneratorFactory::fromLocal()->factory();
$generator = $registry->getGenerator(\PSX\Api\Repository\LocalRepository::CLIENT_PHP)

$source = $generator->generate($resource);

```

## Generator

### Client

- PHP
- Typescript

### Markup

- Client
- HTML
- Markdown

### Spec

- [OpenAPI](https://github.com/OAI/OpenAPI-Specification)
- [TypeAPI](https://typeapi.org/)
