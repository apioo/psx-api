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

namespace PSX\Api\Generator\Markup;

use PSX\Api\Generator\Client\LanguageBuilder;
use PSX\Api\Generator\Client\Util\Naming;
use PSX\Api\Generator\Client\Dto;
use PSX\Api\GeneratorInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Generator\TypeScript;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;

/**
 * Client
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Client implements GeneratorInterface
{
    private SchemaGeneratorInterface $generator;
    private Naming $naming;
    private LanguageBuilder $converter;

    public function __construct()
    {
        $this->generator = new TypeScript();
        $this->naming = new Naming($this->generator->getNormalizer());
        $this->converter = new LanguageBuilder($this->generator, $this->naming);
    }

    public function generate(SpecificationInterface $specification): string
    {
        $client = $this->converter->getClient($specification);

        $lines = [];
        $lines[] = 'var client = new ' . $client->className . '(...)';

        foreach ($client->resources as $resource) {
            /** @var Dto\Resource $resource */
            foreach ($resource->methods as $methodName => $method) {
                /** @var Dto\Resource $method */

                $resourceArguments = implode(', ', array_keys($resource->properties));
                $methodArguments = implode(', ', array_keys(iterator_to_array($method->args ?? [])));

                $lines[] = 'client.' . $resource->methodName . '(' . $resourceArguments . ').' . $methodName . '(' . $methodArguments . ')';
            }

            $lines[] = "\n";
        }

        return implode("\n", $lines);
    }
}
