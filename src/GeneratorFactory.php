<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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
 * GeneratorFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GeneratorFactory implements GeneratorFactoryInterface
{
    protected string $namespace;
    protected string $url;
    protected string $dispatch;

    public function __construct(string $namespace, string $url, string $dispatch)
    {
        $this->namespace = $namespace;
        $this->url       = $url;
        $this->dispatch  = $dispatch;
    }

    public function getGenerator(string $format, ?string $config = null, ?FilterInterface $filter = null): GeneratorInterface
    {
        $baseUri = $this->url . '/' . $this->dispatch;

        switch ($format) {
            case GeneratorFactoryInterface::CLIENT_GO:
                $generator = new Generator\Client\Go($baseUri, $config);
                break;

            case GeneratorFactoryInterface::CLIENT_JAVA:
                $generator = new Generator\Client\Java($baseUri, $config);
                break;

            case GeneratorFactoryInterface::CLIENT_PHP:
                $generator = new Generator\Client\Php($baseUri, $config);
                break;

            case GeneratorFactoryInterface::CLIENT_TYPESCRIPT:
                $generator = new Generator\Client\Typescript($baseUri, $config);
                break;

            case GeneratorFactoryInterface::MARKUP_CLIENT:
                $generator = new Generator\Markup\Client();
                break;

            case GeneratorFactoryInterface::MARKUP_HTML:
                $generator = new Generator\Markup\Html();
                break;

            case GeneratorFactoryInterface::MARKUP_MARKDOWN:
                $generator = new Generator\Markup\Markdown();
                break;

            case GeneratorFactoryInterface::SPEC_RAML:
                $generator = new Generator\Spec\Raml(1, $baseUri);
                break;

            case GeneratorFactoryInterface::SPEC_TYPESCHEMA:
                $generator = new Generator\Spec\TypeSchema();
                break;

            default:
            case GeneratorFactoryInterface::SPEC_OPENAPI:
                $generator = new Generator\Spec\OpenAPI(1, $baseUri);
                break;
        }

        $this->configure($generator, $filter);

        return $generator;
    }

    public function getFileExtension(string $format, ?string $config = null): string
    {
        switch ($format) {
            case GeneratorFactoryInterface::CLIENT_GO:
                return 'go';
            case GeneratorFactoryInterface::CLIENT_JAVA:
                return 'java';
            case GeneratorFactoryInterface::CLIENT_PHP:
                return 'php';
            case GeneratorFactoryInterface::CLIENT_TYPESCRIPT:
                return 'ts';

            case GeneratorFactoryInterface::MARKUP_CLIENT:
                return 'md';
            case GeneratorFactoryInterface::MARKUP_HTML:
                return 'html';
            case GeneratorFactoryInterface::MARKUP_MARKDOWN:
                return 'md';

            case GeneratorFactoryInterface::SPEC_TYPESCHEMA:
            case GeneratorFactoryInterface::SPEC_OPENAPI:
                return 'json';

            case GeneratorFactoryInterface::SPEC_RAML:
                return 'raml';

            default:
                return 'txt';
        }
    }

    public function getMime(string $format, ?string $config = null): string
    {
        switch ($format) {
            case GeneratorFactoryInterface::CLIENT_GO:
                return 'application/go';
            case GeneratorFactoryInterface::CLIENT_JAVA:
                return 'application/java';
            case GeneratorFactoryInterface::CLIENT_PHP:
                return 'application/php';
            case GeneratorFactoryInterface::CLIENT_TYPESCRIPT:
                return 'application/typescript';

            case GeneratorFactoryInterface::MARKUP_CLIENT:
                return 'text/markdown';
            case GeneratorFactoryInterface::MARKUP_HTML:
                return 'text/html';
            case GeneratorFactoryInterface::MARKUP_MARKDOWN:
                return 'text/markdown';

            case GeneratorFactoryInterface::SPEC_TYPESCHEMA:
            case GeneratorFactoryInterface::SPEC_OPENAPI:
                return 'application/json';
            case GeneratorFactoryInterface::SPEC_RAML:
                return 'application/raml+yaml';

            default:
                return 'text/plain';
        }
    }

    /**
     * Callback method to optional configure the created generator
     */
    protected function configure(GeneratorInterface $generator, ?FilterInterface $filter = null): void
    {
    }

    public static function getPossibleTypes(): array
    {
        return [
            GeneratorFactoryInterface::CLIENT_GO,
            GeneratorFactoryInterface::CLIENT_JAVA,
            GeneratorFactoryInterface::CLIENT_PHP,
            GeneratorFactoryInterface::CLIENT_TYPESCRIPT,

            GeneratorFactoryInterface::MARKUP_CLIENT,
            GeneratorFactoryInterface::MARKUP_HTML,
            GeneratorFactoryInterface::MARKUP_MARKDOWN,

            GeneratorFactoryInterface::SPEC_TYPESCHEMA,
            GeneratorFactoryInterface::SPEC_OPENAPI,
            GeneratorFactoryInterface::SPEC_RAML,
        ];
    }
}
