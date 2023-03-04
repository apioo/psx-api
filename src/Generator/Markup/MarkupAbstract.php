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

namespace PSX\Api\Generator\Markup;

use PSX\Api\Exception\GeneratorException;
use PSX\Api\Generator\Client\Dto;
use PSX\Api\Generator\Client\LanguageBuilder;
use PSX\Api\Generator\Client\Util\Naming;
use PSX\Api\GeneratorInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator\NormalizerAwareInterface;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;
use PSX\Schema\Schema;
use PSX\Schema\TypeFactory;

/**
 * MarkupAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class MarkupAbstract implements GeneratorInterface
{
    private SchemaGeneratorInterface $generator;
    private Naming $naming;
    private LanguageBuilder $converter;

    public function __construct()
    {
        $this->generator = $this->newSchemaGenerator();
        if ($this->generator instanceof NormalizerAwareInterface) {
            $this->naming = new Naming($this->generator->getNormalizer());
            $this->converter = new LanguageBuilder($this->generator, $this->naming);
        } else {
            throw new GeneratorException('The provided schema generator must implement the interface: ' . NormalizerAwareInterface::class);
        }
    }

    public function generate(SpecificationInterface $specification): string
    {
        $client = $this->converter->getClient($specification);

        $lines = $this->startLines($client);

        foreach ($client->tags as $tag) {
            /** @var Dto\Tag $tag */
            foreach ($tag->operations as $operation) {
                $lines[] = $this->generateOperation($operation, $tag->methodName);
            }
        }

        foreach ($client->operations as $operation) {
            $lines[] = $this->generateOperation($operation);
        }

        $lines[] = "";
        $lines[] = $this->generateSchema($specification->getDefinitions());

        return implode("\n", $lines);
    }

    abstract protected function generateOperation(Dto\Operation $operation, ?string $tagMethod = null): string;

    abstract protected function newSchemaGenerator(): SchemaGeneratorInterface;

    protected function startLines(Dto\Client $client): array
    {
        return [];
    }

    protected function generateSchema(DefinitionsInterface $definitions): string
    {
        $schema = new Schema(TypeFactory::getAny(), $definitions);
        $return = $this->generator->generate($schema);

        return $return;
    }
}
