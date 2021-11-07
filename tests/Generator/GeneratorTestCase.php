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

use PSX\Api\Resource;
use PSX\Api\SpecificationInterface;
use PSX\Api\Tests\ApiManagerTestCase;
use PSX\Schema\Generator\Code\Chunks;

/**
 * GeneratorTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class GeneratorTestCase extends ApiManagerTestCase
{
    protected function getSpecification(): SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder();
        $resource = $builder->addResource(Resource::STATUS_ACTIVE, '/foo/:name/:type');

        $resource->setTitle('foo');
        $resource->setDescription('lorem ipsum');
        $path = $resource->setPathParameters('Path');
        $path->addString('name')
            ->setDescription('Name parameter')
            ->setMinLength(0)
            ->setMaxLength(16)
            ->setPattern('[A-z]+');
        $path->addString('type')
            ->setEnum(['foo', 'bar']);
        $path->setRequired(['name']);

        $get = $resource->addMethod('GET');
        $get->setDescription('Returns a collection');
        $get->setOperationId('list.foo');
        $query = $get->setQueryParameters('GetQuery');
        $query->addInteger('startIndex')
            ->setDescription('startIndex parameter')
            ->setMinimum(0)
            ->setMaximum(32);
        $query->addNumber('float');
        $query->addBoolean('boolean');
        $query->addDate('date');
        $query->addDateTime('datetime');
        $query->setRequired(['startIndex']);
        $get->addResponse(200, Schema\Collection::class);

        $post = $resource->addMethod('POST');
        $post->setOperationId('create.foo');
        $post->setRequest(Schema\Create::class);
        $post->addResponse(201, Schema\Message::class);

        $put = $resource->addMethod('PUT');
        $put->setRequest(Schema\Update::class);
        $put->addResponse(200, Schema\Message::class);

        $delete = $resource->addMethod('DELETE');
        $delete->setRequest(Schema\Delete::class);
        $delete->addResponse(200, Schema\Message::class);

        $patch = $resource->addMethod('PATCH');
        $patch->setRequest(Schema\Patch::class);
        $patch->addResponse(200, Schema\Message::class);

        return $builder->getSpecification();
    }

    protected function getSpecificationCollection(): SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder();
        $resource = $builder->addResource(Resource::STATUS_ACTIVE, '/foo');
        $resource->setTitle('foo');
        $resource->setTags(['foo']);

        $get = $resource->addMethod('GET');
        $get->setDescription('Returns a collection');
        $get->addResponse(200, Schema\Collection::class);

        $post = $resource->addMethod('POST');
        $post->setRequest(Schema\Create::class);
        $post->addResponse(201, Schema\Message::class);

        $resource = $builder->addResource(Resource::STATUS_ACTIVE, '/bar/:foo');
        $resource->setTitle('bar');
        $resource->setPathParameters('PathFoo')->addString('foo');
        $resource->setTags(['bar']);

        $get = $resource->addMethod('GET');
        $get->setDescription('Returns a collection');
        $get->addResponse(200, Schema\Collection::class);

        $post = $resource->addMethod('POST');
        $post->setRequest(Schema\Create::class);
        $post->addResponse(201, Schema\Message::class);

        $resource = $builder->addResource(Resource::STATUS_ACTIVE, '/bar/$year<[0-9]+>');
        $resource->setTitle('bar');
        $resource->setPathParameters('PathYear')->addString('year');
        $resource->setTags(['bar']);

        $get = $resource->addMethod('GET');
        $get->setDescription('Returns a collection');
        $get->addResponse(200, Schema\Collection::class);

        $post = $resource->addMethod('POST');
        $post->setRequest(Schema\Create::class);
        $post->addResponse(201, Schema\Message::class);

        return $builder->getSpecification();
    }

    protected function getSpecificationComplex(): SpecificationInterface
    {
        $builder = $this->apiManager->getBuilder();
        $resource = $builder->addResource(Resource::STATUS_ACTIVE, '/foo/:name/:type');
        $resource->setTitle('foo');
        $resource->setDescription('lorem ipsum');
        $path = $resource->setPathParameters('Path');
        $path->addString('name');
        $path->addString('type');
        $path->setRequired(['name', 'type']);

        $post = $resource->addMethod('POST');
        $post->setDescription('Returns a collection');
        $post->setOperationId('postEntryOrMessage');
        $post->setTags(['foo']);
        $post->setRequest(Schema\Complex::class);
        $post->addResponse(200, Schema\Complex::class);
        $post->setSecurity('OAuth2', ['foo']);

        return $builder->getSpecification();
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
