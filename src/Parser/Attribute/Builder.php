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

namespace PSX\Api\Parser\Attribute;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use PSX\Api\Attribute\Header;
use PSX\Api\Attribute\OperationId;
use PSX\Api\Attribute\Param;
use PSX\Api\Attribute\Query;

/**
 * Builder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Builder implements BuilderInterface
{
    private CacheItemPoolInterface $cache;
    private bool $debug;

    public function __construct(CacheItemPoolInterface $cache, bool $debug)
    {
        $this->cache = $cache;
        $this->debug = $debug;
    }

    public function buildOperationId(string $controllerClass, string $methodName): string
    {
        $item = null;
        if (!$this->debug) {
            $key = 'psx_operation_id_' . str_replace('\\', '_', $controllerClass) . '_' . $methodName;
            $item = $this->cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $operationId = $this->getByOperationIdAttribute($controllerClass, $methodName);
        if ($operationId === null) {
            $operationId = $this->buildByClassAndMethodName($controllerClass, $methodName);
        }

        if (!$this->debug && $item instanceof CacheItemInterface) {
            $item->set($operationId);
            $this->cache->save($item);
        }

        return $operationId;
    }

    public function buildArguments(string $controllerClass, string $methodName): array
    {
        $item = null;
        if (!$this->debug) {
            $key = 'psx_arguments_' . str_replace('\\', '_', $controllerClass) . '_' . $methodName;
            $item = $this->cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $arguments = $this->getArguments($controllerClass, $methodName);

        if (!$this->debug && $item instanceof CacheItemInterface) {
            $item->set($arguments);
            $this->cache->save($item);
        }

        return $arguments;
    }

    private function getByOperationIdAttribute(string $controllerClass, string $methodName): ?string
    {
        $method = new \ReflectionMethod($controllerClass, $methodName);

        $attributes = $method->getAttributes(OperationId::class);
        foreach ($attributes as $attribute) {
            $operation = $attribute->newInstance();
            if ($operation instanceof OperationId) {
                return $operation->operationId;
            }
        }

        return null;
    }

    private function getArguments(string $controllerClass, string $methodName): array
    {
        $method = new \ReflectionMethod($controllerClass, $methodName);

        $parameters = $method->getParameters();
        $arguments = [];
        foreach ($parameters as $parameter) {
            $pathAttribute = ($parameter->getAttributes(Param::class)[0] ?? null)?->newInstance();
            $headerAttribute = ($parameter->getAttributes(Header::class)[0] ?? null)?->newInstance();
            $queryAttribute = ($parameter->getAttributes(Query::class)[0] ?? null)?->newInstance();

            if ($pathAttribute instanceof Param && !empty($pathAttribute->name)) {
                $arguments[$parameter->getName()] = $pathAttribute->name;
            } elseif ($headerAttribute instanceof Header && !empty($headerAttribute->name)) {
                $arguments[$parameter->getName()] = $headerAttribute->name;
            } elseif ($queryAttribute instanceof Query && !empty($queryAttribute->name)) {
                $arguments[$parameter->getName()] = $queryAttribute->name;
            } else {
                $arguments[$parameter->getName()] = $parameter->getName();
            }
        }

        return $arguments;
    }

    private function buildByClassAndMethodName(string $controllerClass, string $methodName): string
    {
        $result = [];
        $parts = explode('\\', $controllerClass);
        array_shift($parts); // vendor
        array_shift($parts); // controller

        foreach ($parts as $part) {
            $result[] = $this->snakeCase($part);
        }

        $result[] = $methodName;

        return implode('.', $result);
    }

    private function snakeCase(string $name): string
    {
        return strtolower(preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], ['\\1_\\2', '\\1_\\2'], $name));
    }
}
