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

namespace PSX\Api\Generator\Spec;

use PSX\Api\GeneratorAbstract;
use PSX\Api\SpecificationInterface;
use PSX\Json\Parser;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\TypeFactory;

/**
 * TypeSchema
 *
 * @see     https://typeschema.org/
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TypeSchema extends GeneratorAbstract
{
    /**
     * @inheritDoc
     */
    public function generate(SpecificationInterface $specification)
    {
        $collection = $specification->getResourceCollection();
        $definitions = $specification->getDefinitions();

        $resources = [];
        foreach ($collection as $path => $resource) {
            $resources[$path] = $resource->toArray();
        }

        if (count($resources) === 1) {
            $data = reset($resources);
        } else {
            $data = [];
            $data['paths'] = $resources;
        }

        $data['definitions'] = $this->generateDefinitions($definitions);

        return Parser::encode($data, \JSON_PRETTY_PRINT);
    }

    private function generateDefinitions(DefinitionsInterface $definitions): ?array
    {
        $generator = new Generator\TypeSchema();
        $schema = $generator->toArray(TypeFactory::getAny(), $definitions);

        return $schema['definitions'] ?? null;
    }
}
