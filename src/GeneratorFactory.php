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
class GeneratorFactory
{
    const TYPE_HTML = 'html';
    const TYPE_JSONSCHEMA = 'jsonschema';
    const TYPE_MARKDOWN = 'markdown';
    const TYPE_OPENAPI = 'openapi';
    const TYPE_PHP = 'php';
    const TYPE_RAML = 'raml';
    const TYPE_SERIALIZE = 'serialize';
    const TYPE_TEMPLATE = 'template';
    const TYPE_SWAGGER = 'swagger';

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

    public function __construct(Reader $reader, $namespace, $url, $dispatch)
    {
        $this->reader    = $reader;
        $this->namespace = $namespace;
        $this->url       = $url;
        $this->dispatch  = $dispatch;
    }

    /**
     * @param string $format
     * @param string $config
     * @return \PSX\Api\GeneratorInterface
     */
    public function getGenerator($format, $config)
    {
        switch ($format) {
            case self::TYPE_HTML:
                return new Generator\Html();
                break;

            case self::TYPE_JSONSCHEMA:
                $namespace = $config ?: $this->namespace;
                
                return new Generator\JsonSchema($namespace);
                break;

            case self::TYPE_MARKDOWN:
                return new Generator\Markdown();
                break;

            case self::TYPE_OPENAPI:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;
                
                return new Generator\OpenAPI($this->reader, 1, $baseUri, $namespace);
                break;

            case self::TYPE_PHP:
                return new Generator\Php($config ?: null);
                break;

            case self::TYPE_RAML:
                $baseUri   = $this->url . '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;

                return new Generator\Raml('psx', 1, $baseUri, $namespace);
                break;

            case self::TYPE_SERIALIZE:
                return new Generator\Serialize();
                break;

            case self::TYPE_TEMPLATE:
                return new Generator\Template($config);
                break;

            default:
            case self::TYPE_SWAGGER:
                $basePath  = '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;
                
                return new Generator\Swagger($this->reader, 1, $basePath, $namespace);
                break;
        }
    }

    public function getFileExtension($format, $config)
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
