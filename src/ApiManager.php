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
use PSX\Api\Parser\Raml;
use PSX\Cache\Pool;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;

/**
 * ApiManager
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiManager
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var Parser\Annotation
     */
    protected $parser;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var boolean
     */
    protected $debug;
    
    public function __construct(Reader $reader = null, SchemaManagerInterface $schemaManager, CacheItemPoolInterface $cache = null, $debug = false)
    {
        $this->reader = $reader;
        $this->parser = new Parser\Annotation($reader, $schemaManager);
        $this->cache  = $cache === null ? new Pool(new ArrayCache()) : $cache;
        $this->debug  = $debug;
    }

    public function getApi($source, $path)
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

        if (strpos($source, '.raml') !== false) {
            $api = Raml::fromFile($source, $path);
        } elseif (class_exists($source)) {
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
}
