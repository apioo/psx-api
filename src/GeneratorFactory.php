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
            case GeneratorFactoryInterface::CLIENT_GO:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $generator = new Generator\Client\Go($baseUri, $config);
                break;

            case GeneratorFactoryInterface::CLIENT_JAVA:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $generator = new Generator\Client\Java($baseUri, $config);
                break;

            case GeneratorFactoryInterface::CLIENT_PHP:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $generator = new Generator\Client\Php($baseUri, $config);
                break;

            case GeneratorFactoryInterface::CLIENT_TYPESCRIPT:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $generator = new Generator\Client\Typescript($baseUri, $config);
                break;

            case GeneratorFactoryInterface::MARKUP_HTML:
                $generator = new Generator\Markup\Html();
                break;

            case GeneratorFactoryInterface::MARKUP_MARKDOWN:
                $generator = new Generator\Markup\Markdown();
                break;

            case GeneratorFactoryInterface::SPEC_RAML:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $generator = new Generator\Spec\Raml(1, $baseUri);
                break;

            case GeneratorFactoryInterface::SPEC_TYPESCHEMA:
                $generator = new Generator\Spec\TypeSchema();
                break;

            default:
            case GeneratorFactoryInterface::SPEC_OPENAPI:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $generator = new Generator\Spec\OpenAPI($this->reader, 1, $baseUri);
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
            case GeneratorFactoryInterface::CLIENT_GO:
                return 'go';
            case GeneratorFactoryInterface::CLIENT_JAVA:
                return 'java';
            case GeneratorFactoryInterface::CLIENT_PHP:
                return 'php';
            case GeneratorFactoryInterface::CLIENT_TYPESCRIPT:
                return 'ts';

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

    /**
     * @inheritdoc
     */
    public function getMime($format, $config = null)
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
            GeneratorFactoryInterface::CLIENT_GO,
            GeneratorFactoryInterface::CLIENT_JAVA,
            GeneratorFactoryInterface::CLIENT_PHP,
            GeneratorFactoryInterface::CLIENT_TYPESCRIPT,

            GeneratorFactoryInterface::MARKUP_HTML,
            GeneratorFactoryInterface::MARKUP_MARKDOWN,

            GeneratorFactoryInterface::SPEC_TYPESCHEMA,
            GeneratorFactoryInterface::SPEC_OPENAPI,
            GeneratorFactoryInterface::SPEC_RAML,
        ];
    }
}
