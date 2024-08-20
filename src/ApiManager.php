<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\Builder\SpecificationBuilder;
use PSX\Api\Builder\SpecificationBuilderInterface;
use PSX\Api\Exception\InvalidApiException;
use PSX\Schema\Parser\ContextInterface;
use PSX\Schema\SchemaManagerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * ApiManager
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ApiManager implements ApiManagerInterface
{
    private CacheItemPoolInterface $cache;
    private bool $debug;

    private array $parsers = [];

    public function __construct(SchemaManagerInterface $schemaManager, ?CacheItemPoolInterface $cache = null, bool $debug = false)
    {
        $this->cache = $cache === null ? new ArrayAdapter() : $cache;
        $this->debug = $debug;

        $this->register('php', new Parser\Attribute($schemaManager));
        $this->register('file', new Parser\File($schemaManager));
        $this->register('openapi', new Parser\OpenAPI($schemaManager));
        $this->register('typeapi', new Parser\TypeAPI($schemaManager));
    }

    public function register(string $scheme, ParserInterface $parser): void
    {
        $this->parsers[$scheme] = $parser;
    }

    public function getApi(string $source, ?ContextInterface $context = null): SpecificationInterface
    {
        $item = null;
        if (!$this->debug) {
            $item = $this->cache->getItem('psx-api-' . md5($source));
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $pos = strpos($source, '://');
        if ($pos === false) {
            $source = $this->guessSchemeFromSource($source);
            $pos = strpos($source, '://');
        }

        if ($pos === false) {
            throw new InvalidApiException('Could not resolve api uri');
        }

        $scheme = substr($source, 0, $pos);
        $value = substr($source, $pos + 3);
        if (isset($this->parsers[$scheme])) {
            $api = $this->parsers[$scheme]->parse($value, $context);
        } else {
            throw new InvalidApiException('API ' . $source . ' does not exist');
        }

        if (!$this->debug && $item !== null) {
            $item->set($api);
            $this->cache->save($item);
        }

        return $api;
    }

    public function clear(string $source): void
    {
        $this->cache->deleteItem('psx-api-' . md5($source));
    }

    public function getBuilder(): SpecificationBuilderInterface
    {
        return new SpecificationBuilder();
    }

    private function guessSchemeFromSource(string $source): ?string
    {
        if (class_exists($source)) {
            return 'php://' . str_replace('\\', '.', $source);
        } elseif (is_file($source)) {
            return 'file://' . $source;
        } else {
            return $source;
        }
    }
}
