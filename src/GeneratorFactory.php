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
            case self::TYPE_HTML:
                $generator = new Generator\Html();
                break;

            case self::TYPE_JSONSCHEMA:
                $namespace = $config ?: $this->namespace;
                $generator = new Generator\JsonSchema($namespace);
                break;

            case self::TYPE_MARKDOWN:
                $generator = new Generator\Markdown();
                break;

            case self::TYPE_OPENAPI:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;
                $generator = new Generator\OpenAPI($this->reader, 1, $baseUri, $namespace);
                break;

            case self::TYPE_PHP:
                return new Generator\Php($config ?: null);
                break;

            case self::TYPE_RAML:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;
                $generator = new Generator\Raml('psx', 1, $baseUri, $namespace);
                break;

            case self::TYPE_SERIALIZE:
                $generator = new Generator\Serialize();
                break;

            case self::TYPE_TEMPLATE:
                $generator = new Generator\Template($config);
                break;

            default:
            case self::TYPE_SWAGGER:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;
                $generator = new Generator\Swagger($this->reader, 1, $baseUri, $namespace);
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
            case self::TYPE_HTML:
                return 'html';

            case self::TYPE_MARKDOWN:
                return 'md';

            case self::TYPE_PHP:
                return 'php';

            case self::TYPE_RAML:
                return 'raml';

            case self::TYPE_SERIALIZE:
                return 'ser';

            case self::TYPE_TEMPLATE:
                $ext = pathinfo(pathinfo($config, PATHINFO_FILENAME), PATHINFO_EXTENSION);
                return !empty($ext) ? $ext : 'html';

            case self::TYPE_JSONSCHEMA:
            case self::TYPE_OPENAPI:
            case self::TYPE_SWAGGER:
                return 'json';

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
            case self::TYPE_HTML:
                return 'text/html';

            case self::TYPE_MARKDOWN:
                return 'text/markdown';

            case self::TYPE_PHP:
                return 'application/php';

            case self::TYPE_RAML:
                return 'application/raml+yaml';

            case self::TYPE_SERIALIZE:
                return 'application/octet-stream';

            case self::TYPE_TEMPLATE:
                return 'text/plain';

            case self::TYPE_JSONSCHEMA:
            case self::TYPE_OPENAPI:
            case self::TYPE_SWAGGER:
                return 'application/json';

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
            self::TYPE_HTML,
            self::TYPE_JSONSCHEMA,
            self::TYPE_MARKDOWN,
            self::TYPE_OPENAPI,
            self::TYPE_PHP,
            self::TYPE_RAML,
            self::TYPE_SERIALIZE,
            self::TYPE_TEMPLATE,
            self::TYPE_SWAGGER
        ];
    }
}
