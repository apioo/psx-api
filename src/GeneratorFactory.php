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

use PSX\Api\Scanner\FilterInterface;

/**
 * GeneratorFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GeneratorFactory implements GeneratorFactoryInterface
{
    private string $url;
    private string $dispatch;

    public function __construct(string $url, string $dispatch)
    {
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

            case GeneratorFactoryInterface::SPEC_TYPEAPI:
                $generator = new Generator\Spec\TypeAPI();
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
        return match ($format) {
            GeneratorFactoryInterface::CLIENT_GO => 'go',
            GeneratorFactoryInterface::CLIENT_JAVA => 'java',
            GeneratorFactoryInterface::CLIENT_PHP => 'php',
            GeneratorFactoryInterface::CLIENT_TYPESCRIPT => 'ts',
            GeneratorFactoryInterface::MARKUP_CLIENT => 'md',
            GeneratorFactoryInterface::MARKUP_HTML => 'html',
            GeneratorFactoryInterface::MARKUP_MARKDOWN => 'md',
            GeneratorFactoryInterface::SPEC_TYPEAPI => 'json',
            GeneratorFactoryInterface::SPEC_OPENAPI => 'json',
            default => 'txt',
        };
    }

    public function getMime(string $format, ?string $config = null): string
    {
        return match ($format) {
            GeneratorFactoryInterface::CLIENT_GO => 'application/go',
            GeneratorFactoryInterface::CLIENT_JAVA => 'application/java',
            GeneratorFactoryInterface::CLIENT_PHP => 'application/php',
            GeneratorFactoryInterface::CLIENT_TYPESCRIPT => 'application/typescript',
            GeneratorFactoryInterface::MARKUP_CLIENT => 'text/markdown',
            GeneratorFactoryInterface::MARKUP_HTML => 'text/html',
            GeneratorFactoryInterface::MARKUP_MARKDOWN => 'text/markdown',
            GeneratorFactoryInterface::SPEC_TYPEAPI => 'application/json',
            GeneratorFactoryInterface::SPEC_OPENAPI => 'application/json',
            default => 'text/plain',
        };
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

            GeneratorFactoryInterface::SPEC_TYPEAPI,
            GeneratorFactoryInterface::SPEC_OPENAPI,
        ];
    }
}
