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
            case 'html':
                return new Generator\Html();
                break;

            case 'jsonschema':
                $namespace = $config ?: $this->namespace;
                
                return new Generator\JsonSchema($namespace);
                break;

            case 'markdown':
                return new Generator\Markdown();
                break;

            case 'openapi':
                $baseUri   = $this->url . '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;
                
                return new Generator\OpenAPI($this->reader, 1, $baseUri, $namespace);
                break;

            case 'php':
                return new Generator\Php($config ?: null);
                break;

            case 'raml':
                $baseUri   = $this->url . '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;

                return new Generator\Raml('psx', 1, $baseUri, $namespace);
                break;

            case 'serialize':
                return new Generator\Serialize();
                break;

            case 'template':
                return new Generator\Template($config);
                break;

            default:
            case 'swagger':
                $basePath  = '/' . $this->dispatch;
                $namespace = $config ?: $this->namespace;
                
                return new Generator\Swagger($this->reader, 1, $basePath, $namespace);
                break;
        }
    }

    public function getFileExtension($format, $config)
    {
        switch ($format) {
            case 'html':
                return 'html';

            case 'markdown':
                return 'md';

            case 'php':
                return 'php';

            case 'raml':
                return 'raml';

            case 'serialize':
                return 'ser';

            case 'template':
                $ext = pathinfo(pathinfo($config, PATHINFO_FILENAME), PATHINFO_EXTENSION);
                return !empty($ext) ? $ext : 'html';

            case 'jsonschema':
            case 'openapi':
            case 'swagger':
                return 'json';

            default:
                return 'txt';
        }
    }

    public static function getPossibleTypes()
    {
        return [
            'html',
            'jsonschema',
            'markdown',
            'openapi',
            'php',
            'raml',
            'serialize',
            'template',
            'swagger'
        ];
    }
}
