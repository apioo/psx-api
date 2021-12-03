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
use Prophecy\Doubler\ClassPatch\ReflectionClassNewInstancePatch;
use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\Builder\SpecificationBuilder;
use PSX\Api\Builder\SpecificationBuilderInterface;
use PSX\Api\Parser\OpenAPI;
use PSX\Schema\SchemaManagerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * ApiManager
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiManager implements ApiManagerInterface
{
    public const TYPE_ANNOTATION = 1;
    public const TYPE_ATTRIBUTE = 2;
    public const TYPE_OPENAPI = 3;

    private SchemaManagerInterface $schemaManager;
    private Parser\Annotation $annotationParser;
    private Parser\Attribute $attributeParser;
    private CacheItemPoolInterface $cache;
    private bool $debug;

    public function __construct(Reader $reader, SchemaManagerInterface $schemaManager, CacheItemPoolInterface $cache = null, bool $debug = false)
    {
        $this->schemaManager = $schemaManager;
        $this->annotationParser = new Parser\Annotation($reader, $schemaManager);
        $this->attributeParser = new Parser\Attribute($schemaManager);
        $this->cache = $cache === null ? new ArrayAdapter() : $cache;
        $this->debug = $debug;
    }

    /**
     * @inheritdoc
     */
    public function getApi(string $source, ?string $path, ?int $type = null): SpecificationInterface
    {
        $item = null;
        if (!$this->debug) {
            $item = $this->cache->getItem(md5($source));
            if ($item->isHit()) {
                return $item->get();
            }
        }

        if ($type === null) {
            $type = $this->guessTypeFromSource($source);
        }

        if ($type === self::TYPE_OPENAPI) {
            $api = OpenAPI::fromFile($source, $path);
        } elseif ($type === self::TYPE_ANNOTATION) {
            $api = $this->annotationParser->parse($source, $path);
        } elseif ($type === self::TYPE_ATTRIBUTE) {
            $api = $this->attributeParser->parse($source, $path);
        } else {
            throw new \RuntimeException('Schema ' . $source . ' does not exist');
        }

        if (!$this->debug && $item !== null) {
            $item->set($api);
            $this->cache->save($item);
        }

        return $api;
    }

    /**
     * @inheritDoc
     */
    public function getBuilder(): SpecificationBuilderInterface
    {
        return new SpecificationBuilder($this->schemaManager);
    }

    private function guessTypeFromSource($source): ?int
    {
        if (class_exists($source)) {
            if (PHP_VERSION_ID > 80000 && count((new \ReflectionClass($source))->getAttributes()) > 0) {
                return self::TYPE_ATTRIBUTE;
            } else {
                return self::TYPE_ANNOTATION;
            }
        } else {
            return self::TYPE_OPENAPI;
        }
    }
}
