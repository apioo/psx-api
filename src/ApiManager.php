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
use Doctrine\Common\Cache\ArrayCache;
use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\Parser\OpenAPI;
use PSX\Api\Parser\Raml;
use PSX\Api\Parser\Swagger;
use PSX\Cache\Pool;
use PSX\Schema\SchemaManagerInterface;

/**
 * ApiManager
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiManager implements ApiManagerInterface
{
    const TYPE_ANNOTATION = 1;
    const TYPE_RAML = 2;
    const TYPE_OPENAPI = 3;
    const TYPE_SWAGGER = 4;

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $reader;

    /**
     * @var \PSX\Api\Parser\Annotation
     */
    protected $parser;

    /**
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var boolean
     */
    protected $debug;

    /**
     * @param \Doctrine\Common\Annotations\Reader $reader
     * @param \PSX\Schema\SchemaManagerInterface $schemaManager
     * @param \Psr\Cache\CacheItemPoolInterface|null $cache
     * @param bool $debug
     */
    public function __construct(Reader $reader, SchemaManagerInterface $schemaManager, CacheItemPoolInterface $cache = null, $debug = false)
    {
        $this->reader = $reader;
        $this->parser = new Parser\Annotation($reader, $schemaManager);
        $this->cache  = $cache === null ? new Pool(new ArrayCache()) : $cache;
        $this->debug  = $debug;
    }

    /**
     * @inheritdoc
     */
    public function getApi($source, $path, $type = null)
    {
        if (!is_string($source)) {
            throw new \InvalidArgumentException('API name must be a string');
        }

        $item = null;
        if (!$this->debug) {
            $item = $this->cache->getItem($source);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        if ($type === null) {
            $type = $this->guessTypeFromSource($source);
        }

        if ($type === self::TYPE_RAML) {
            $api = Raml::fromFile($source, $path);
        } elseif ($type === self::TYPE_OPENAPI) {
            $api = OpenAPI::fromFile($source, $path);
        } elseif ($type === self::TYPE_SWAGGER) {
            $api = Swagger::fromFile($source, $path);
        } elseif ($type === self::TYPE_ANNOTATION) {
            $api = $this->parser->parse($source, $path);
        } else {
            throw new \RuntimeException('Schema ' . $source . ' does not exist');
        }

        if (!$this->debug && $item !== null) {
            $item->set($api);
            $this->cache->save($item);
        }

        return $api;
    }

    private function guessTypeFromSource($source)
    {
        if (strpos($source, '.raml') !== false) {
            return self::TYPE_RAML;
        } elseif (strpos($source, '.json') !== false) {
            return self::TYPE_OPENAPI;
        } elseif (class_exists($source)) {
            return self::TYPE_ANNOTATION;
        } else {
            return null;
        }
    }
}
