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

namespace PSX\Api;

use PSX\Api\Listing\FilterInterface;

/**
 * GeneratorFactoryInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface GeneratorFactoryInterface
{
    public const CLIENT_GO = 'client-go';
    public const CLIENT_JAVA = 'client-java';
    public const CLIENT_PHP = 'client-php';
    public const CLIENT_TYPESCRIPT = 'client-typescript';

    public const MARKUP_CLIENT = 'markup-client';
    public const MARKUP_HTML = 'markup-html';
    public const MARKUP_MARKDOWN = 'markup-markdown';

    public const SPEC_TYPEAPI = 'spec-typeapi';
    public const SPEC_OPENAPI = 'spec-openapi';
    public const SPEC_RAML = 'spec-raml';

    /**
     * Returns the fitting generator object for the provided type
     */
    public function getGenerator(string $format, ?string $config = null, ?FilterInterface $filter = null): GeneratorInterface;

    /**
     * Returns the preferred file extension for the provided format
     */
    public function getFileExtension(string $format, ?string $config = null): string;

    /**
     * Returns the preferred mime for the provided format
     */
    public function getMime(string $format, ?string $config = null): string;
}
