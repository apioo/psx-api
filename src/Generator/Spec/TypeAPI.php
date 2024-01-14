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

namespace PSX\Api\Generator\Spec;

use PSX\Api\GeneratorInterface;
use PSX\Api\SecurityInterface;
use PSX\Api\SpecificationInterface;
use PSX\Json\Parser;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\TypeFactory;

/**
 * TypeAPI
 *
 * @see     https://typeschema.org/
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TypeAPI implements GeneratorInterface
{
    private ?string $baseUrl;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl;
    }

    public function generate(SpecificationInterface $specification): Generator\Code\Chunks|string
    {
        $operations = $specification->getOperations();
        $definitions = $specification->getDefinitions();

        $data = [];

        if (!empty($this->baseUrl)) {
            $data['baseUrl'] = $this->baseUrl;
        }

        $security = $specification->getSecurity();
        if ($security instanceof SecurityInterface) {
            $data['security'] = $security;
        }

        $data['operations'] = $operations;
        $data['definitions'] = $this->generateDefinitions($definitions);

        return Parser::encode($data);
    }

    private function generateDefinitions(DefinitionsInterface $definitions): ?array
    {
        $generator = new Generator\TypeSchema();
        $schema = $generator->toArray(TypeFactory::getAny(), $definitions);

        return $schema['definitions'] ?? null;
    }
}
