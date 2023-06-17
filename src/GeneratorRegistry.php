<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\Repository\GeneratorConfig;
use PSX\Api\Scanner\FilterInterface;

/**
 * The generator registry contains all available generators
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GeneratorRegistry
{
    /**
     * @var iterable<ConfiguratorInterface>
     */
    protected iterable $configurators;
    protected ?string $baseUrl;

    /**
     * @var GeneratorConfig[]
     */
    private array $generators = [];

    public function __construct(iterable $configurators = [], ?string $baseUrl = null)
    {
        $this->configurators = $configurators;
        $this->baseUrl = $baseUrl;
    }

    public function register(string $type, GeneratorConfig $generatorConfig): void
    {
        $this->generators[$type] = $generatorConfig;
    }

    public function getGenerator(string $type, ?string $config = null, ?FilterInterface $filter = null): GeneratorInterface
    {
        $generator = $this->getGeneratorConfig($type)->newInstance($this->baseUrl, $config);

        foreach ($this->configurators as $configurator) {
            if ($configurator->accept($generator)) {
                $configurator->configure($generator, $filter);
            }
        }

        return $generator;
    }

    public function getFileExtension(string $type): string
    {
        return $this->getGeneratorConfig($type)->getFileExtension();
    }

    public function getMime(string $type): string
    {
        return $this->getGeneratorConfig($type)->getMime();
    }

    public function getPossibleTypes(): array
    {
        return array_keys($this->generators);
    }

    private function getGeneratorConfig(string $format): GeneratorConfig
    {
        if (!isset($this->generators[$format])) {
            $format = array_key_last($this->generators);
        }

        return $this->generators[$format];
    }
}
