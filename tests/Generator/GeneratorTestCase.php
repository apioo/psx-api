<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Schema\Format;
use PSX\Schema\Generator\Code\Chunks;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;

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
        $operation->addArgument('name', ArgumentInterface::IN_PATH, TypeFactory::getString()
            ->setDescription('Name parameter')
            ->setMinLength(0)
            ->setMaxLength(16)
            ->setPattern('[A-z]+'));
        $operation->addArgument('type', ArgumentInterface::IN_PATH, TypeFactory::getString()
            ->setEnum(['foo', 'bar']));
        $operation->addArgument('startIndex', ArgumentInterface::IN_QUERY, TypeFactory::getInteger()
            ->setDescription('startIndex parameter')
            ->setMinimum(0)
            ->setMaximum(32));
        $operation->addArgument('float', ArgumentInterface::IN_QUERY, TypeFactory::getNumber());
        $operation->addArgument('boolean', ArgumentInterface::IN_QUERY, TypeFactory::getBoolean());
        $operation->addArgument('date', ArgumentInterface::IN_QUERY, TypeFactory::getString()
            ->setFormat(Format::DATE));
        $operation->addArgument('datetime', ArgumentInterface::IN_QUERY, TypeFactory::getString()
            ->setFormat(Format::DATETIME));
        $operation->addArgument('args', ArgumentInterface::IN_QUERY, TypeFactory::getReference('Entry'));

        $operation = $builder->addOperation('create', 'POST', '/foo/:name/:type', 201, $message);
        $operation->addArgument('name', ArgumentInterface::IN_PATH, TypeFactory::getString());
        $operation->addArgument('type', ArgumentInterface::IN_PATH, TypeFactory::getString());
        $operation->addArgument('payload', ArgumentInterface::IN_BODY, $create);
        $operation->addThrow(400, $message);
        $operation->addThrow(500, $message);

        $operation = $builder->addOperation('update', 'PUT', '/foo/:name/:type', 200, TypeFactory::getMap($message));
        $operation->addArgument('name', ArgumentInterface::IN_PATH, TypeFactory::getString());
        $operation->addArgument('type', ArgumentInterface::IN_PATH, TypeFactory::getString());
        $operation->addArgument('payload', ArgumentInterface::IN_BODY, TypeFactory::getMap($update));
        $operation->addThrow(400, $message);
        $operation->addThrow(500, TypeFactory::getMap($message));

        $operation = $builder->addOperation('delete', 'DELETE', '/foo/:name/:type', 204, $message);
        $operation->addArgument('name', ArgumentInterface::IN_PATH, TypeFactory::getString());
        $operation->addArgument('type', ArgumentInterface::IN_PATH, TypeFactory::getString());
        $operation->addArgument('payload', ArgumentInterface::IN_BODY, $delete);

        $operation = $builder->addOperation('patch', 'PATCH', '/foo/:name/:type', 200, TypeFactory::getArray($message));
        $operation->addArgument('name', ArgumentInterface::IN_PATH, TypeFactory::getString());
        $operation->addArgument('type', ArgumentInterface::IN_PATH, TypeFactory::getString());
        $operation->addArgument('payload', ArgumentInterface::IN_BODY, TypeFactory::getArray($patch));
        $operation->addThrow(400, $message);
        $operation->addThrow(500, TypeFactory::getArray($message));

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
        $operation->addArgument('foo', 'path', TypeFactory::getString());

        $operation = $builder->addOperation('bar.put', 'POST', '/bar/:foo', 201, $message);
        $operation->setTags(['bar']);
        $operation->addArgument('payload', 'body', $create);

        $operation = $builder->addOperation('foo.baz.get', 'GET', '/bar/$year<[0-9]+>', 200, $collection);
        $operation->setDescription('Returns a collection');
        $operation->setTags(['baz']);
        $operation->addArgument('year', 'path', TypeFactory::getString());

        $operation = $builder->addOperation('foo.baz.create', 'POST', '/bar/$year<[0-9]+>', 201, $message);
        $operation->setTags(['baz']);
        $operation->addArgument('payload', 'body', $create);

        return $builder->getSpecification();
    }

    protected function getSpecificationComplex(): SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder();
        $builder->setSecurity(new HttpBearer());

        $complex = $this->addSchema($builder, Schema\Complex::class);

        $operation = $builder->addOperation('get', 'GET', '/foo/:name/:type', 200, $complex);
        $operation->setDescription('Returns a collection');
        $operation->setTags(['foo']);
        $operation->setSecurity(['foo']);
        $operation->addArgument('name', 'path', TypeFactory::getString());
        $operation->addArgument('type', 'path', TypeFactory::getString());
        $operation->addArgument('payload', 'body', $complex);

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
        $operation->addArgument('startIndex', 'query', TypeFactory::getInteger());
        $operation->addArgument('count', 'query', TypeFactory::getInteger());
        $operation->addArgument('search', 'query', TypeFactory::getString());

        $operation = $builder->addOperation('product.create', 'POST', '/anything', 200, $testResponse);
        $operation->setDescription('Creates a new product');
        $operation->addArgument('payload', 'body', $testRequest);

        $operation = $builder->addOperation('product.update', 'PUT', '/anything/:id', 200, $testResponse);
        $operation->setDescription('Updates an existing product');
        $operation->addArgument('id', 'path', TypeFactory::getInteger());
        $operation->addArgument('payload', 'body', $testRequest);

        $operation = $builder->addOperation('product.patch', 'PATCH', '/anything/:id', 200, $testResponse);
        $operation->setDescription('Patches an existing product');
        $operation->addArgument('id', 'path', TypeFactory::getInteger());
        $operation->addArgument('payload', 'body', $testRequest);

        $operation = $builder->addOperation('product.delete', 'DELETE', '/anything/:id', 200, $testResponse);
        $operation->setDescription('Deletes an existing product');
        $operation->addArgument('id', 'path', TypeFactory::getInteger());

        return $builder->getSpecification();
    }

    protected function getPaths(): array
    {
        return [];
    }

    protected function writeChunksToFolder(Chunks $result, string $target): void
    {
        foreach ($result->getChunks() as $file => $code) {
            file_put_contents($target . '/' . $file, $code);
        }
    }

    private function addSchema(SpecificationBuilderInterface $builder, string $schema): TypeInterface
    {
        $result = $this->schemaManager->getSchema($schema);
        $builder->addDefinitions($result->getDefinitions());
        return $result->getType();
    }
}
