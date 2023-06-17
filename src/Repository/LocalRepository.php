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

namespace PSX\Api\Repository;

use PSX\Api\Generator;

/**
 * LocalRepository
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class LocalRepository implements RepositoryInterface
{
    public const CLIENT_PHP = 'client-php';
    public const CLIENT_TYPESCRIPT = 'client-typescript';

    public const MARKUP_CLIENT = 'markup-client';
    public const MARKUP_HTML = 'markup-html';
    public const MARKUP_MARKDOWN = 'markup-markdown';

    public const SPEC_TYPEAPI = 'spec-typeapi';
    public const SPEC_OPENAPI = 'spec-openapi';

    public function getAll(): array
    {
        $result = [];

        $result[self::CLIENT_PHP] = new GeneratorConfig(
            fn(?string $baseUrl, ?string $config) => new Generator\Client\Php($baseUrl, $config),
            'php',
            'application/php'
        );

        $result[self::CLIENT_TYPESCRIPT] = new GeneratorConfig(
            fn(?string $baseUrl, ?string $config) => new Generator\Client\Typescript($baseUrl, $config),
            'ts',
            'application/typescript'
        );

        $result[self::MARKUP_CLIENT] = new GeneratorConfig(
            fn(?string $baseUrl, ?string $config) => new Generator\Markup\Client(),
            'md',
            'text/markdown'
        );

        $result[self::MARKUP_HTML] = new GeneratorConfig(
            fn(?string $baseUrl, ?string $config) => new Generator\Markup\Html(),
            'html',
            'text/html'
        );

        $result[self::MARKUP_MARKDOWN] = new GeneratorConfig(
            fn(?string $baseUrl, ?string $config) => new Generator\Markup\Markdown(),
            'md',
            'text/markdown'
        );

        $result[self::SPEC_TYPEAPI] = new GeneratorConfig(
            fn(?string $baseUrl, ?string $config) => new Generator\Spec\TypeAPI($baseUrl),
            'json',
            'application/json'
        );

        $result[self::SPEC_OPENAPI] = new GeneratorConfig(
            fn(?string $baseUrl, ?string $config) => new Generator\Spec\OpenAPI(1, $baseUrl),
            'json',
            'application/json'
        );

        return $result;
    }
}
