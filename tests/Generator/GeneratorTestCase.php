<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PHPUnit\Framework\TestCase;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Builder;
use PSX\Schema\Definitions;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator\Code\Chunks;
use PSX\Schema\SchemaManager;
use PSX\Schema\TypeFactory;

/**
 * GeneratorTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class GeneratorTestCase extends TestCase
{
    protected function getSpecification(): SpecificationInterface
    {
        return Specification::fromResource(
            $this->newResource(),
            $this->newDefinitions()
        );
    }

    private function newResource(): Resource
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, '/foo/:name/:type');
        $resource->setTitle('foo');
        $resource->setDescription('lorem ipsum');
        $resource->setPathParameters('Path');

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->setOperationId('list.foo')
            ->setQueryParameters('GetQuery')
            ->addResponse(200, 'EntryCollection'));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setOperationId('create.foo')
            ->setRequest('EntryCreate')
            ->addResponse(201, 'EntryMessage'));

        $resource->addMethod(Resource\Factory::getMethod('PUT')
            ->setRequest('EntryUpdate')
            ->addResponse(200, 'EntryMessage'));

        $resource->addMethod(Resource\Factory::getMethod('DELETE')
            ->setRequest('EntryDelete')
            ->addResponse(200, 'EntryMessage'));

        $resource->addMethod(Resource\Factory::getMethod('PATCH')
            ->setRequest('EntryPatch')
            ->addResponse(200, 'EntryMessage'));

        return $resource;
    }

    private function newDefinitions(): DefinitionsInterface
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');

        $schemaManager = new SchemaManager($reader);

        $builder = new Builder();
        $builder->addString('name')
            ->setDescription('Name parameter')
            ->setMinLength(0)
            ->setMaxLength(16)
            ->setPattern('[A-z]+');
        $builder->addString('type')
            ->setEnum(['foo', 'bar']);
        $builder->setRequired(['name']);
        $path = $builder->getType();

        $builder = new Builder();
        $builder->addInteger('startIndex')
            ->setDescription('startIndex parameter')
            ->setMinimum(0)
            ->setMaximum(32);
        $builder->addNumber('float');
        $builder->addBoolean('boolean');
        $builder->addDate('date');
        $builder->addDateTime('datetime');
        $builder->setRequired(['startIndex']);
        $getQuery = $builder->getType();

        $definitions = new Definitions();
        $definitions->addType('Path', $path);
        $definitions->addType('GetQuery', $getQuery);
        $definitions->merge($schemaManager->getSchema(Schema\Collection::class)->getDefinitions());
        $definitions->merge($schemaManager->getSchema(Schema\Create::class)->getDefinitions());
        $definitions->merge($schemaManager->getSchema(Schema\Update::class)->getDefinitions());
        $definitions->merge($schemaManager->getSchema(Schema\Delete::class)->getDefinitions());
        $definitions->merge($schemaManager->getSchema(Schema\Patch::class)->getDefinitions());
        $definitions->merge($schemaManager->getSchema(Schema\Message::class)->getDefinitions());

        return $definitions;
    }

    protected function getSpecificationCollection(): SpecificationInterface
    {
        return new Specification(
            $this->newResourceCollection(),
            $this->newDefinitionsCollection()
        );
    }

    private function newResourceCollection()
    {
        $collection = new ResourceCollection();

        $resource = new Resource(Resource::STATUS_ACTIVE, '/foo');
        $resource->setTitle('foo');

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->addResponse(200, 'EntryCollection'));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest('EntryCreate')
            ->addResponse(201, 'EntryMessage'));

        $collection->set($resource);

        $resource = new Resource(Resource::STATUS_ACTIVE, '/bar/:foo');
        $resource->setTitle('bar');
        $resource->setPathParameters('PathFoo');
        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->addResponse(200, 'EntryCollection'));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest('EntryCreate')
            ->addResponse(201, 'EntryMessage'));

        $collection->set($resource);

        $resource = new Resource(Resource::STATUS_ACTIVE, '/bar/$year<[0-9]+>');
        $resource->setTitle('bar');
        $resource->setPathParameters('PathYear');
        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->addResponse(200, 'EntryCollection'));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest('EntryCreate')
            ->addResponse(201, 'EntryMessage'));

        $collection->set($resource);

        return $collection;
    }

    private function newDefinitionsCollection(): DefinitionsInterface
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');

        $schemaManager = new SchemaManager($reader);

        $builder = new Builder();
        $builder->addString('foo');
        $pathFoo = $builder->getType();

        $builder = new Builder();
        $builder->addString('year');
        $pathYear = $builder->getType();

        $definitions = new Definitions();
        $definitions->addType('PathFoo', $pathFoo);
        $definitions->addType('PathYear', $pathYear);
        $definitions->merge($schemaManager->getSchema(Schema\Collection::class)->getDefinitions());
        $definitions->merge($schemaManager->getSchema(Schema\Create::class)->getDefinitions());
        $definitions->merge($schemaManager->getSchema(Schema\Message::class)->getDefinitions());

        return $definitions;
    }

    protected function getSpecificationComplex(): SpecificationInterface
    {
        return Specification::fromResource(
            $this->newResourceComplex(),
            $this->newDefinitionsComplex()
        );
    }

    private function newResourceComplex(): Resource
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, '/foo/:name/:type');
        $resource->setTitle('foo');
        $resource->setDescription('lorem ipsum');
        $resource->setPathParameters('Path');

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setDescription('Returns a collection')
            ->setOperationId('postEntryOrMessage')
            ->setTags(['foo'])
            ->setRequest('EntryOrMessage')
            ->addResponse(200, 'EntryOrMessage')
            ->setSecurity('OAuth2', ['foo']));

        return $resource;
    }

    private function newDefinitionsComplex(): DefinitionsInterface
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');

        $schemaManager = new SchemaManager($reader);

        $builder = new Builder();
        $builder->addString('name');
        $builder->addString('type');
        $builder->setRequired(['name', 'type']);
        $path = $builder->getType();

        $definitions = new Definitions();
        $definitions->addType('Path', $path);
        $definitions->merge($schemaManager->getSchema(Schema\Complex::class)->getDefinitions());

        return $definitions;
    }

    protected function getPaths()
    {
        return array();
    }

    protected function writeChunksToFolder(Chunks $result, string $target)
    {
        foreach ($result->getChunks() as $file => $code) {
            $code = str_replace(date('Y-m-d'), '0000-00-00', $code);

            file_put_contents($target . '/' . $file, $code);
        }
    }
}
