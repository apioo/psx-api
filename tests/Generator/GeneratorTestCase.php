<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Schema\Property;
use PSX\Schema\SchemaManager;

/**
 * GeneratorTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class GeneratorTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getResource()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');

        $schemaManager = new SchemaManager($reader);

        $resource = new Resource(Resource::STATUS_ACTIVE, '/foo/:name/:type');
        $resource->setTitle('foo');
        $resource->setDescription('lorem ipsum');

        $resource->addPathParameter('name', Property::getString()
            ->setDescription('Name parameter')
            ->setMinLength(0)
            ->setMaxLength(16)
            ->setPattern('[A-z]+'));
        $resource->addPathParameter('type', Property::getString()
            ->setEnum(['foo', 'bar']));

        $resource->getPathParameters()->setRequired(['name']);
        
        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->addQueryParameter('startIndex', Property::getInteger()
                ->setDescription('startIndex parameter')
                ->setMinimum(0)
                ->setMaximum(32))
            ->addQueryParameter('float', Property::getNumber())
            ->addQueryParameter('boolean', Property::getBoolean())
            ->addQueryParameter('date', Property::getDate())
            ->addQueryParameter('datetime', Property::getDateTime())
            ->addResponse(200, $schemaManager->getSchema(Schema\Collection::class)));

        $resource->getMethod('GET')->getQueryParameters()->setRequired(['startIndex']);

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($schemaManager->getSchema(Schema\Create::class))
            ->addResponse(201, $schemaManager->getSchema(Schema\SuccessMessage::class)));

        $resource->addMethod(Resource\Factory::getMethod('PUT')
            ->setRequest($schemaManager->getSchema(Schema\Update::class))
            ->addResponse(200, $schemaManager->getSchema(Schema\SuccessMessage::class)));

        $resource->addMethod(Resource\Factory::getMethod('DELETE')
            ->setRequest($schemaManager->getSchema(Schema\Delete::class))
            ->addResponse(200, $schemaManager->getSchema(Schema\SuccessMessage::class)));

        $resource->addMethod(Resource\Factory::getMethod('PATCH')
            ->setRequest($schemaManager->getSchema(Schema\Patch::class))
            ->addResponse(200, $schemaManager->getSchema(Schema\SuccessMessage::class)));

        return $resource;
    }

    protected function getResourceCollection()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');

        $schemaManager = new SchemaManager($reader);

        $collection = new ResourceCollection();

        $resource = new Resource(Resource::STATUS_ACTIVE, '/foo');
        $resource->setTitle('foo');

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->addResponse(200, $schemaManager->getSchema(Schema\Collection::class)));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($schemaManager->getSchema(Schema\Create::class))
            ->addResponse(201, $schemaManager->getSchema(Schema\SuccessMessage::class)));

        $collection->set($resource);

        $resource = new Resource(Resource::STATUS_ACTIVE, '/bar/:foo');
        $resource->setTitle('bar');

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->addResponse(200, $schemaManager->getSchema(Schema\Collection::class)));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($schemaManager->getSchema(Schema\Create::class))
            ->addResponse(201, $schemaManager->getSchema(Schema\SuccessMessage::class)));

        $collection->set($resource);

        $resource = new Resource(Resource::STATUS_ACTIVE, '/bar/$year<[0-9]+>');
        $resource->setTitle('bar');

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->addResponse(200, $schemaManager->getSchema(Schema\Collection::class)));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($schemaManager->getSchema(Schema\Create::class))
            ->addResponse(201, $schemaManager->getSchema(Schema\SuccessMessage::class)));

        $collection->set($resource);

        return $collection;
    }

    protected function getSecurityResource()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');

        $schemaManager = new SchemaManager($reader);

        $resource = new Resource(Resource::STATUS_ACTIVE, '/foo/:name/:type');
        $resource->setTitle('foo');
        $resource->setDescription('lorem ipsum');

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->addResponse(200, $schemaManager->getSchema(Schema\Collection::class))
            ->setSecurity('OAuth2', ['foo']));

        return $resource;
    }

    protected function getPaths()
    {
        return array();
    }
}
