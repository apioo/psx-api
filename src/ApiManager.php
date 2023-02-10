<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\Builder\SpecificationBuilder;
use PSX\Api\Builder\SpecificationBuilderInterface;
use PSX\Api\Exception\ParserException;
use PSX\Api\Parser\OpenAPI;
use PSX\Api\Parser\TypeAPI;
use PSX\Schema\SchemaManagerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Yaml\Yaml;

/**
 * ApiManager
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ApiManager implements ApiManagerInterface
{
    public const TYPE_ATTRIBUTE = 1;
    public const TYPE_OPENAPI = 2;
    public const TYPE_TYPEAPI = 3;

    private SchemaManagerInterface $schemaManager;
    private Parser\Attribute $attributeParser;
    private CacheItemPoolInterface $cache;
    private bool $debug;

    public function __construct(SchemaManagerInterface $schemaManager, CacheItemPoolInterface $cache = null, bool $debug = false)
    {
        $this->schemaManager = $schemaManager;
        $this->attributeParser = new Parser\Attribute($schemaManager);
        $this->cache = $cache === null ? new ArrayAdapter() : $cache;
        $this->debug = $debug;
    }

    public function getApi(string $source, ?int $type = null): SpecificationInterface
    {
        $item = null;
        if (!$this->debug) {
            $item = $this->cache->getItem(md5($source));
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $basePath = null;
        $data = null;
        if (class_exists($source)) {
            $type = self::TYPE_ATTRIBUTE;
        } elseif (is_file($source)) {
            $basePath  = pathinfo($source, PATHINFO_DIRNAME);
            $extension = pathinfo($source, PATHINFO_EXTENSION);
            if (in_array($extension, ['yaml', 'yml'])) {
                $data = json_decode(json_encode(Yaml::parse(file_get_contents($source))));
            } else {
                $data = json_decode(file_get_contents($source));
            }

            if (!$data instanceof \stdClass) {
                throw new ParserException('Provided source must be an JSON or YAML file containing an object');
            }

            if (isset($data->paths)) {
                $type = self::TYPE_OPENAPI;
            } elseif (isset($data->operations)) {
                $type = self::TYPE_TYPEAPI;
            } else {
                throw new ParserException('Could not detect schema format of the provided source ' . $source);
            }
        } else {
            throw new ParserException('Provided source must be either a class or a file');
        }

        if ($type === self::TYPE_OPENAPI) {
            $api = (new OpenAPI($basePath))->parseObject($data);
        } elseif ($type === self::TYPE_TYPEAPI) {
            $api = (new TypeAPI($basePath))->parseObject($data);
        } elseif ($type === self::TYPE_ATTRIBUTE) {
            $api = $this->attributeParser->parse($source);
        } else {
            throw new ParserException('Schema ' . $source . ' does not exist');
        }

        if (!$this->debug && $item !== null) {
            $item->set($api);
            $this->cache->save($item);
        }

        return $api;
    }

    public function getBuilder(): SpecificationBuilderInterface
    {
        return new SpecificationBuilder($this->schemaManager);
    }

    private function guessTypeFromSource(string $source): ?int
    {
        if (class_exists($source)) {
            return self::TYPE_ATTRIBUTE;
        }

        if (is_file($source)) {
            $extension = pathinfo($source, PATHINFO_EXTENSION);
            if (in_array($extension, ['yaml', 'yml'])) {
                $data = json_decode(json_encode(Yaml::parse(file_get_contents($source))));
            } else {
                $data = json_decode(file_get_contents($source));
            }

            if (!$data instanceof \stdClass) {
                throw new ParserException('Provided source must be an JSON or YAML file containing an object');
            }

            if (isset($data->paths)) {
                return self::TYPE_OPENAPI;
            } elseif (isset($data->operations)) {
                return self::TYPE_TYPEAPI;
            } else {
                throw new ParserException('Could not detect schema format of the provided source ' . $source);
            }
        }

        throw new ParserException('Provided source must be either a class or a file');
    }
}
