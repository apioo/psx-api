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

use PSX\Api\Repository\LocalRepository;
use PSX\Api\Repository\RepositoryInterface;

/**
 * This factory returns a GeneratorRegistry which contains all available generator types and which can be used to obtain
 * an actual generator
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GeneratorFactory
{
    /**
     * @var iterable<RepositoryInterface>
     */
    private iterable $repositories;

    /**
     * @var iterable<ConfiguratorInterface>
     */
    private iterable $configurators;
    private ?string $baseUrl;

    private ?GeneratorRegistry $generatorRegistry = null;

    public function __construct(iterable $repositories, iterable $configurators = [], ?string $baseUrl = null)
    {
        $this->repositories = $repositories;
        $this->configurators = $configurators;
        $this->baseUrl = $baseUrl;
    }

    public function factory(?string $baseUrl = null): GeneratorRegistry
    {
        if (isset($this->generatorRegistry)) {
            return $this->generatorRegistry;
        }

        $this->generatorRegistry = new GeneratorRegistry($this->configurators, $baseUrl ?? $this->baseUrl);

        foreach ($this->repositories as $repository) {
            $generatorConfigs = $repository->getAll();
            foreach ($generatorConfigs as $type => $generatorConfig) {
                $this->generatorRegistry->register($type, $generatorConfig);
            }
        }

        return $this->generatorRegistry;
    }

    /**
     * Returns a new generator factory which contains only the local generators
     */
    public static function fromLocal(?string $baseUrl = null): self
    {
        return new self([new LocalRepository()], [], $baseUrl);
    }
}
