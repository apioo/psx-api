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

namespace PSX\Api\Generator\Spec;

use PSX\Api\Generator\ConfigurationAwareInterface;
use PSX\Api\Generator\ConfigurationTrait;
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
 * @see     https://typeapi.org/
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TypeAPI implements GeneratorInterface, ConfigurationAwareInterface
{
    use ConfigurationTrait;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl;
    }

    public function generate(SpecificationInterface $specification): Generator\Code\Chunks|string
    {
        $operations = $specification->getOperations();
        $definitions = $specification->getDefinitions();

        $data = [];

        $baseUrl = $this->getBaseUrl($specification);
        if (!empty($baseUrl)) {
            $data['baseUrl'] = $baseUrl;
        }

        $security = $this->getSecurity($specification);
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
        $schema = $generator->toArray($definitions, null);

        return $schema['definitions'] ?? null;
    }
}
