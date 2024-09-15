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

namespace PSX\Api\Tests\Parser\Attribute;

use Psr\Http\Message\StreamInterface;
use PSX\Api\Attribute\Body;
use PSX\Api\Attribute\OperationId;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Data\Body\Form;
use PSX\Data\Body\Json;
use PSX\Data\Body\Multipart;
use PSX\Http\Stream\StringStream;

/**
 * ContentTypeController
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ContentTypeController
{
    #[Post]
    #[Path('/binary')]
    #[OperationId('binary')]
    protected function binary(#[Body] StreamInterface $body): StreamInterface
    {
        return new StringStream('foo');
    }

    #[Post]
    #[Path('/text')]
    #[OperationId('text')]
    protected function text(#[Body] string $body): string
    {
        return 'foo';
    }

    #[Post]
    #[Path('/form')]
    #[OperationId('form')]
    protected function form(#[Body] Form $body): Form
    {
        return $body;
    }

    #[Post]
    #[Path('/multipart')]
    #[OperationId('multipart')]
    protected function multipart(#[Body] Multipart $body): Multipart
    {
        return $body;
    }

    #[Post]
    #[Path('/json')]
    #[OperationId('json')]
    protected function json(#[Body] Json $body): Json
    {
        return $body;
    }

    #[Post]
    #[Path('/xml')]
    #[OperationId('xml')]
    protected function xml(#[Body] \DOMDocument $body): \DOMDocument
    {
        return new \DOMDocument();
    }
}
