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

use PSX\Api\Generator\Client\Dto;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator\TypeScript;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;

/**
 * Client
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Client extends MarkupAbstract
{
    protected function generateOperation(Dto\Operation $operation, ?string $tagMethod = null): string
    {
        $args = [];
        foreach ($operation->arguments as $argumentName => $argument) {
            $args[] = $argumentName . ': ' . $argument->schema->type;
        }

        $return = $operation->return?->schema->type ?? 'void';
        $throws = $this->getThrows($operation);

        if ($tagMethod !== null) {
            return 'client.' . $tagMethod . '().' . $operation->methodName . '(' . implode(', ', $args) . '): ' . $return . $throws;
        } else {
            return 'client.' . $operation->methodName . '(' . implode(', ', $args) . '): ' . $return . $throws;
        }
    }

    protected function startLines(Dto\Client $client): array
    {
        $lines = [];
        $lines[] = 'const client = new ' . $client->className . '()';

        return $lines;
    }

    protected function generateSchema(DefinitionsInterface $definitions): string
    {
        $return = parent::generateSchema($definitions);
        $return = str_replace('export interface', 'interface', $return);
        $return = preg_replace('/^import(.*);$/ims', '', $return);
        $return = str_replace("\n\n\n", "\n\n", $return);

        return $return;
    }

    protected function newSchemaGenerator(): SchemaGeneratorInterface
    {
        return new TypeScript();
    }

    private function getThrows(Dto\Operation $operation): string
    {
        $throws = [];
        foreach ($operation->throws as $throw) {
            $throws[] = $throw->schema->type;
        }

        $throws = array_unique($throws);

        if (count($throws) > 0) {
            return ' throws ' . implode(', ', $throws);
        } else {
            return '';
        }
    }
}
