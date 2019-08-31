<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Doctrine\Common\Annotations\Reader;

/**
 * GeneratorFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GeneratorFactory implements GeneratorFactoryInterface
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $dispatch;

    /**
     * @param \Doctrine\Common\Annotations\Reader $reader
     * @param string $namespace
     * @param string $url
     * @param string $dispatch
     */
    public function __construct(Reader $reader, $namespace, $url, $dispatch)
    {
        $this->reader    = $reader;
        $this->namespace = $namespace;
        $this->url       = $url;
        $this->dispatch  = $dispatch;
    }

    /**
     * @inheritdoc
     */
    public function getGenerator($format, $config = null)
    {
        switch ($format) {
            case GeneratorFactoryInterface::CLIENT_PHP:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $generator = new Generator\Client\Php($baseUri);
                break;

            case GeneratorFactoryInterface::CLIENT_TYPESCRIPT:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $generator = new Generator\Client\Typescript($baseUri);
                break;

            case GeneratorFactoryInterface::MARKUP_HTML:
                $generator = new Generator\Markup\Html();
                break;

            case GeneratorFactoryInterface::MARKUP_MARKDOWN:
                $generator = new Generator\Markup\Markdown();
                break;

            case GeneratorFactoryInterface::MARKUP_TEMPLATE:
                $generator = new Generator\Markup\Template($config);
                break;

            case GeneratorFactoryInterface::SERVER_PHP:
                return new Generator\Server\Php($config ?: null);
                break;

            case GeneratorFactoryInterface::SPEC_JSONSCHEMA:
                $namespace = $config ?: $this->namespace;
                $generator = new Generator\Spec\JsonSchema($namespace);
                break;

            case GeneratorFactoryInterface::SPEC_RAML:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;
                $generator = new Generator\Spec\Raml(1, $baseUri, $namespace);
                break;

            case GeneratorFactoryInterface::SPEC_SWAGGER:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;
                $generator = new Generator\Spec\Swagger($this->reader, 1, $baseUri, $namespace);
                break;

            default:
            case GeneratorFactoryInterface::SPEC_OPENAPI:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;
                $generator = new Generator\Spec\OpenAPI($this->reader, 1, $baseUri, $namespace);
                break;
        }

        $this->configure($generator);

        return $generator;
    }

    /**
     * @inheritdoc
     */
    public function getFileExtension($format, $config = null)
    {
        switch ($format) {
            case GeneratorFactoryInterface::CLIENT_PHP:
                return 'php';
            case GeneratorFactoryInterface::CLIENT_TYPESCRIPT:
                return 'ts';

            case GeneratorFactoryInterface::MARKUP_HTML:
                return 'html';
            case GeneratorFactoryInterface::MARKUP_MARKDOWN:
                return 'md';
            case GeneratorFactoryInterface::MARKUP_TEMPLATE:
                $ext = pathinfo(pathinfo($config, PATHINFO_FILENAME), PATHINFO_EXTENSION);
                return !empty($ext) ? $ext : 'html';

            case GeneratorFactoryInterface::SERVER_PHP:
                return 'php';

            case GeneratorFactoryInterface::SPEC_JSONSCHEMA:
            case GeneratorFactoryInterface::SPEC_OPENAPI:
            case GeneratorFactoryInterface::SPEC_SWAGGER:
                return 'json';

            case GeneratorFactoryInterface::SPEC_RAML:
                return 'raml';

            default:
                return 'txt';
        }
    }

    /**
     * @inheritdoc
     */
    public function getMime($format, $config = null)
    {
        switch ($format) {
            case GeneratorFactoryInterface::CLIENT_PHP:
                return 'application/php';
            case GeneratorFactoryInterface::CLIENT_TYPESCRIPT:
                return 'application/typescript';

            case GeneratorFactoryInterface::MARKUP_HTML:
                return 'text/html';
            case GeneratorFactoryInterface::MARKUP_MARKDOWN:
                return 'text/markdown';
            case GeneratorFactoryInterface::MARKUP_TEMPLATE:
                return 'text/plain';

            case GeneratorFactoryInterface::SERVER_PHP:
                return 'application/php';

            case GeneratorFactoryInterface::SPEC_JSONSCHEMA:
            case GeneratorFactoryInterface::SPEC_OPENAPI:
            case GeneratorFactoryInterface::SPEC_SWAGGER:
                return 'application/json';

            case GeneratorFactoryInterface::SPEC_RAML:
                return 'application/raml+yaml';

            default:
                return 'text/plain';
        }
    }

    /**
     * Callback method to optional configure the created generator
     * 
     * @param \PSX\Api\GeneratorInterface $generator
     */
    protected function configure(GeneratorInterface $generator)
    {
    }

    /**
     * @return array
     */
    public static function getPossibleTypes()
    {
        return [
            GeneratorFactoryInterface::CLIENT_PHP,
            GeneratorFactoryInterface::CLIENT_TYPESCRIPT,

            GeneratorFactoryInterface::MARKUP_HTML,
            GeneratorFactoryInterface::MARKUP_MARKDOWN,
            GeneratorFactoryInterface::MARKUP_TEMPLATE,

            GeneratorFactoryInterface::SERVER_PHP,

            GeneratorFactoryInterface::SPEC_JSONSCHEMA,
            GeneratorFactoryInterface::SPEC_OPENAPI,
            GeneratorFactoryInterface::SPEC_RAML,
            GeneratorFactoryInterface::SPEC_SWAGGER,
        ];
    }
}
