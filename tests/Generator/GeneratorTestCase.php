<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Api\Tests\Generator;

use PSX\Api\Builder\SpecificationBuilderInterface;
use PSX\Api\Operation\ArgumentInterface;
use PSX\Api\Security\HttpBearer;
use PSX\Api\SpecificationInterface;
use PSX\Api\Tests\ApiManagerTestCase;
use PSX\Schema\ContentType;
use PSX\Schema\Definitions;
use PSX\Schema\Format;
use PSX\Schema\Generator\Code\Chunks;
use PSX\Schema\Type\Factory\DefinitionTypeFactory;
use PSX\Schema\Type\Factory\PropertyTypeFactory;
use PSX\Schema\Type\ReferencePropertyType;

/**
 * GeneratorTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class GeneratorTestCase extends ApiManagerTestCase
{
    protected function getSpecification(): SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder();
        $builder->setSecurity(new HttpBearer());

        $collection = $this->addSchema($builder, Schema\Collection::class);
        $message = $this->addSchema($builder, Schema\Message::class);
        $create = $this->addSchema($builder, Schema\Create::class);
        $update = $this->addSchema($builder, Schema\Update::class);
        $delete = $this->addSchema($builder, Schema\Delete::class);
        $patch = $this->addSchema($builder, Schema\Patch::class);

        $operation = $builder->addOperation('get', 'GET', '/foo/:name/:type', 200, $collection);
        $operation->setDescription('Returns a collection');
        $operation->addArgument('name', ArgumentInterface::IN_PATH, PropertyTypeFactory::getString()
            ->setDescription('Name parameter'));
        $operation->addArgument('type', ArgumentInterface::IN_PATH, PropertyTypeFactory::getString());
        $operation->addArgument('startIndex', ArgumentInterface::IN_QUERY, PropertyTypeFactory::getInteger()
            ->setDescription('startIndex parameter'));
        $operation->addArgument('float', ArgumentInterface::IN_QUERY, PropertyTypeFactory::getNumber());
        $operation->addArgument('boolean', ArgumentInterface::IN_QUERY, PropertyTypeFactory::getBoolean());
        $operation->addArgument('date', ArgumentInterface::IN_QUERY, PropertyTypeFactory::getString()
            ->setFormat(Format::DATE));
        $operation->addArgument('datetime', ArgumentInterface::IN_QUERY, PropertyTypeFactory::getString()
            ->setFormat(Format::DATETIME));
        $operation->addArgument('args', ArgumentInterface::IN_QUERY, PropertyTypeFactory::getReference('Entry'));

        $operation = $builder->addOperation('create', 'POST', '/foo/:name/:type', 201, $message);
        $operation->addArgument('name', ArgumentInterface::IN_PATH, PropertyTypeFactory::getString());
        $operation->addArgument('type', ArgumentInterface::IN_PATH, PropertyTypeFactory::getString());
        $operation->addArgument('payload', ArgumentInterface::IN_BODY, $create);
        $operation->addThrow(400, $message);
        $operation->addThrow(500, $message);

        $operation = $builder->addOperation('update', 'PUT', '/foo/:name/:type', 200, PropertyTypeFactory::getMap($message));
        $operation->addArgument('name', ArgumentInterface::IN_PATH, PropertyTypeFactory::getString());
        $operation->addArgument('type', ArgumentInterface::IN_PATH, PropertyTypeFactory::getString());
        $operation->addArgument('payload', ArgumentInterface::IN_BODY, PropertyTypeFactory::getMap($update));
        $operation->addThrow(400, $message);
        $operation->addThrow(500, PropertyTypeFactory::getMap($message));

        $operation = $builder->addOperation('delete', 'DELETE', '/foo/:name/:type', 204, $message);
        $operation->addArgument('name', ArgumentInterface::IN_PATH, PropertyTypeFactory::getString());
        $operation->addArgument('type', ArgumentInterface::IN_PATH, PropertyTypeFactory::getString());
        $operation->addArgument('payload', ArgumentInterface::IN_BODY, $delete);

        $operation = $builder->addOperation('patch', 'PATCH', '/foo/:name/:type', 200, PropertyTypeFactory::getArray($message));
        $operation->addArgument('name', ArgumentInterface::IN_PATH, PropertyTypeFactory::getString());
        $operation->addArgument('type', ArgumentInterface::IN_PATH, PropertyTypeFactory::getString());
        $operation->addArgument('payload', ArgumentInterface::IN_BODY, PropertyTypeFactory::getArray($patch));
        $operation->addThrow(400, $message);
        $operation->addThrow(500, PropertyTypeFactory::getArray($message));

        return $builder->getSpecification();
    }

    protected function getSpecificationCollection(): SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder();
        $builder->setSecurity(new HttpBearer());

        $collection = $this->addSchema($builder, Schema\Collection::class);
        $message = $this->addSchema($builder, Schema\Message::class);
        $create = $this->addSchema($builder, Schema\Create::class);

        $operation = $builder->addOperation('foo.bar.get', 'GET', '/foo', 200, $collection);
        $operation->setDescription('Returns a collection');
        $operation->setTags(['foo']);

        $operation = $builder->addOperation('foo.bar.create', 'POST', '/foo', 201, $message);
        $operation->setTags(['foo']);
        $operation->addArgument('payload', 'body', $create);
        $operation->addThrow(400, $message);
        $operation->addThrow(500, $message);

        $operation = $builder->addOperation('bar.find', 'GET', '/bar/:foo', 200, $collection);
        $operation->setDescription('Returns a collection');
        $operation->setTags(['bar']);
        $operation->addArgument('foo', 'path', PropertyTypeFactory::getString());

        $operation = $builder->addOperation('bar.put', 'POST', '/bar/:foo', 201, $message);
        $operation->setTags(['bar']);
        $operation->addArgument('payload', 'body', $create);

        $operation = $builder->addOperation('foo.baz.get', 'GET', '/bar/$year<[0-9]+>', 200, $collection);
        $operation->setDescription('Returns a collection');
        $operation->setTags(['baz']);
        $operation->addArgument('year', 'path', PropertyTypeFactory::getString());

        $operation = $builder->addOperation('foo.baz.create', 'POST', '/bar/$year<[0-9]+>', 201, $message);
        $operation->setTags(['baz']);
        $operation->addArgument('payload', 'body', $create);

        return $builder->getSpecification();
    }

    protected function getSpecificationTest(): SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder();
        $builder->setSecurity(new HttpBearer());

        $testRequest = $this->addSchema($builder, Schema\TestRequest::class);
        $testResponse = $this->addSchema($builder, Schema\TestResponse::class);

        $operation = $builder->addOperation('product.getAll', 'GET', '/anything', 200, $testResponse);
        $operation->setDescription('Returns a collection');
        $operation->addArgument('startIndex', 'query', PropertyTypeFactory::getInteger());
        $operation->addArgument('count', 'query', PropertyTypeFactory::getInteger());
        $operation->addArgument('search', 'query', PropertyTypeFactory::getString());

        $operation = $builder->addOperation('product.create', 'POST', '/anything', 200, $testResponse);
        $operation->setDescription('Creates a new product');
        $operation->addArgument('payload', 'body', $testRequest);
        $operation->addThrow(500, $testResponse);

        $operation = $builder->addOperation('product.update', 'PUT', '/anything/:id', 200, $testResponse);
        $operation->setDescription('Updates an existing product');
        $operation->addArgument('id', 'path', PropertyTypeFactory::getInteger());
        $operation->addArgument('payload', 'body', $testRequest);

        $operation = $builder->addOperation('product.patch', 'PATCH', '/anything/:id', 200, $testResponse);
        $operation->setDescription('Patches an existing product');
        $operation->addArgument('id', 'path', PropertyTypeFactory::getInteger());
        $operation->addArgument('payload', 'body', $testRequest);

        $operation = $builder->addOperation('product.delete', 'DELETE', '/anything/:id', 200, $testResponse);
        $operation->setDescription('Deletes an existing product');
        $operation->addArgument('id', 'path', PropertyTypeFactory::getInteger());

        $operation = $builder->addOperation('product.binary', 'POST', '/anything/binary', 200, $testResponse);
        $operation->setDescription('Test binary content type');
        $operation->addArgument('payload', 'body', new ContentType(ContentType::BINARY));
        $operation->addThrow(500, new ContentType(ContentType::BINARY));

        $operation = $builder->addOperation('product.form', 'POST', '/anything/form', 200, $testResponse);
        $operation->setDescription('Test form content type');
        $operation->addArgument('payload', 'body', new ContentType(ContentType::FORM));
        $operation->addThrow(500, new ContentType(ContentType::FORM));

        $operation = $builder->addOperation('product.json', 'POST', '/anything/json', 200, $testResponse);
        $operation->setDescription('Test json content type');
        $operation->addArgument('payload', 'body', new ContentType(ContentType::JSON));
        $operation->addThrow(500, new ContentType(ContentType::JSON));

        $operation = $builder->addOperation('product.multipart', 'POST', '/anything/multipart', 200, $testResponse);
        $operation->setDescription('Test json content type');
        $operation->addArgument('payload', 'body', new ContentType(ContentType::MULTIPART));
        $operation->addThrow(500, new ContentType(ContentType::MULTIPART));

        $operation = $builder->addOperation('product.text', 'POST', '/anything/text', 200, $testResponse);
        $operation->setDescription('Test text content type');
        $operation->addArgument('payload', 'body', new ContentType(ContentType::TEXT));
        $operation->addThrow(500, new ContentType(ContentType::TEXT));

        $operation = $builder->addOperation('product.xml', 'POST', '/anything/xml', 200, $testResponse);
        $operation->setDescription('Test xml content type');
        $operation->addArgument('payload', 'body', new ContentType(ContentType::XML));
        $operation->addThrow(500, new ContentType(ContentType::XML));

        return $builder->getSpecification();
    }

    protected function getSpecificationContentType(): SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder();

        $operation = $builder->addOperation('binary', 'POST', '/binary', 200, new ContentType(ContentType::BINARY));
        $operation->addArgument('body', 'body', new ContentType(ContentType::BINARY));
        $operation->addThrow(999, new ContentType(ContentType::BINARY));

        $operation = $builder->addOperation('form', 'POST', '/form', 200, new ContentType(ContentType::FORM));
        $operation->addArgument('body', 'body', new ContentType(ContentType::FORM));
        $operation->addThrow(599, new ContentType(ContentType::FORM));

        $operation = $builder->addOperation('json', 'POST', '/json', 200, new ContentType(ContentType::JSON));
        $operation->addArgument('body', 'body', new ContentType(ContentType::JSON));
        $operation->addThrow(499, new ContentType(ContentType::JSON));

        $operation = $builder->addOperation('multipart', 'POST', '/multipart', 200, new ContentType(ContentType::MULTIPART));
        $operation->addArgument('body', 'body', new ContentType(ContentType::MULTIPART));
        $operation->addThrow(500, new ContentType(ContentType::MULTIPART));

        $operation = $builder->addOperation('text', 'POST', '/text', 200, new ContentType(ContentType::TEXT));
        $operation->addArgument('body', 'body', new ContentType(ContentType::TEXT));
        $operation->addThrow(500, new ContentType(ContentType::TEXT));

        $operation = $builder->addOperation('xml', 'POST', '/xml', 200, new ContentType(ContentType::XML));
        $operation->addArgument('body', 'body', new ContentType(ContentType::XML));
        $operation->addThrow(500, new ContentType(ContentType::XML));

        return $builder->getSpecification();
    }

    protected function getSpecificationImport(): SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder();

        $definitions = new Definitions();
        $definitions->addType('import:my_type', DefinitionTypeFactory::getStruct()->addProperty('foo', PropertyTypeFactory::getString()));
        $definitions->addType('my_schema', DefinitionTypeFactory::getStruct()->addProperty('foo', PropertyTypeFactory::getReference('import:my_type')));
        $builder->addDefinitions($definitions);

        $operation = $builder->addOperation('foo', 'GET', '/anything', 200, PropertyTypeFactory::getReference('import:my_type'));
        $operation->addArgument('body', 'body', PropertyTypeFactory::getReference('import:my_type'));
        $operation->addThrow(500, PropertyTypeFactory::getReference('import:my_type'));

        return $builder->getSpecification();
    }

    protected function getPaths(): array
    {
        return [];
    }

    protected function writeChunksToFolder(Chunks $result, string $target): void
    {
        iterator_to_array($result->writeToFolder($target));
    }

    private function addSchema(SpecificationBuilderInterface $builder, string $schema): ReferencePropertyType
    {
        $result = $this->schemaManager->getSchema($schema);
        $builder->addDefinitions($result->getDefinitions());
        return PropertyTypeFactory::getReference($result->getRoot());
    }
}
