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
use PSX\Api\OperationInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\ContentType;
use PSX\Schema\Generator;
use PSX\Schema\Schema;
use PSX\Schema\Type\PropertyTypeAbstract;
use PSX\Schema\TypeInterface;

/**
 * GraphQL
 *
 * @see     https://graphql.org/
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GraphQL implements GeneratorInterface, ConfigurationAwareInterface
{
    use ConfigurationTrait;

    private Generator\GraphQL $generator;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl;
        $this->generator = new Generator\GraphQL();
    }

    public function generate(SpecificationInterface $specification): Generator\Code\Chunks|string
    {
        $operations = $specification->getOperations();
        $definitions = $specification->getDefinitions();

        $result = '';

        $result.= 'schema {' . "\n";
        $result.= '  query: Query' . "\n";
        $result.= '}' . "\n";
        $result.= "\n";

        $result.= 'type Query {' . "\n";

        foreach ($operations->getAll() as $operationName => $operation) {
            if ($operation->getMethod() !== 'GET') {
                continue;
            }

            $result.= '  ' . $this->toArguments($operationName, $operation) . "\n";
        }

        $result.= '}' . "\n";
        $result.= "\n";

        $result.= $this->generator->generate(new Schema($definitions, null));

        return $result;
    }

    private function toArguments(string $operationName, OperationInterface $operation): string
    {
        $methodName = $this->generator->getNormalizer()->method($operationName);

        $arguments = [];
        foreach ($operation->getArguments()->getAll() as $argumentName => $argument) {
            $argumentName = $this->generator->getNormalizer()->argument($argumentName);

            $arguments[] = $argumentName . ': ' . $this->toType($argument->getSchema());
        }

        $return = $this->toType($operation->getReturn()->getSchema());

        return $methodName . '(' . implode(', ', $arguments) . '): ' . $return;
    }

    private function toType(ContentType|TypeInterface $schema): ?string
    {
        if ($schema instanceof ContentType) {
            return null;
        }

        if (!$schema instanceof PropertyTypeAbstract) {
            return null;
        }

        return $this->generator->getTypeGenerator()->getType($schema);
    }
}
