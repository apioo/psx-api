<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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
use PSX\Schema\Property;
use PSX\Schema\SchemaManager;

/**
 * GeneratorTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
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

        $resource = new Resource(Resource::STATUS_ACTIVE, '/foo/bar');
        $resource->setTitle('foo');
        $resource->setDescription('lorem ipsum');

        $resource->addPathParameter('name', Property::getString()
            ->setDescription('Name parameter')
            ->setRequired(false)
            ->setMinLength(0)
            ->setMaxLength(16)
            ->setPattern('[A-z]+'));
        $resource->addPathParameter('type', Property::getString()
            ->setEnumeration(['foo', 'bar']));

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->setDescription('Returns a collection')
            ->addQueryParameter('startIndex', Property::getInteger()
                ->setDescription('startIndex parameter')
                ->setRequired(false)
                ->setMin(0)
                ->setMax(32))
            ->addQueryParameter('float', Property::getFloat())
            ->addQueryParameter('boolean', Property::getBoolean())
            ->addQueryParameter('date', Property::getDate())
            ->addQueryParameter('datetime', Property::getDateTime())
            ->addResponse(200, $schemaManager->getSchema('PSX\Api\Tests\Generator\Schema\Collection')));

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($schemaManager->getSchema('PSX\Api\Tests\Generator\Schema\Create'))
            ->addResponse(201, $schemaManager->getSchema('PSX\Api\Tests\Generator\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('PUT')
            ->setRequest($schemaManager->getSchema('PSX\Api\Tests\Generator\Schema\Update'))
            ->addResponse(200, $schemaManager->getSchema('PSX\Api\Tests\Generator\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('DELETE')
            ->setRequest($schemaManager->getSchema('PSX\Api\Tests\Generator\Schema\Delete'))
            ->addResponse(200, $schemaManager->getSchema('PSX\Api\Tests\Generator\Schema\SuccessMessage')));

        $resource->addMethod(Resource\Factory::getMethod('PATCH')
            ->setRequest($schemaManager->getSchema('PSX\Api\Tests\Generator\Schema\Patch'))
            ->addResponse(200, $schemaManager->getSchema('PSX\Api\Tests\Generator\Schema\SuccessMessage')));

        return $resource;
    }

    protected function getPaths()
    {
        return array();
    }
}
