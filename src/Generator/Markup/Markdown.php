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
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;

/**
 * Markdown
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Markdown extends MarkupAbstract
{
    protected function generateOperation(Dto\Operation $operation, ?string $tagMethod = null): string
    {
        $return = '# ' . $operation->methodName . "\n";
        $return.= '`' . $operation->method . ' ' . $operation->path . '`' . "\n";
        $return.= "\n";

        $description = $operation->description;
        if (!empty($description)) {
            $return.= '> ' . $description;
            $return.= "\n";
        }

        $return.= "\n";
        $return.= '## Request' . "\n";
        $return.= "\n";
        $return.= '<table>';
        $return.= '<colgroup>';
        $return.= '<col width="40%" />';
        $return.= '<col width="40%" />';
        $return.= '<col width="20%" />';
        $return.= '</colgroup>';
        $return.= '<thead>';
        $return.= '<tr>';
        $return.= '<th>Name</th>';
        $return.= '<th>Type</th>';
        $return.= '<th>Location</th>';
        $return.= '</tr>';
        $return.= '</thead>';
        $return.= '<tbody>';

        foreach ($operation->arguments as $argumentName => $argument) {
            $return.= '<tr>';
            $return.= '<td>' . $argumentName . '</td>';
            $return.= '<td>' . $argument->schema->type . '</td>';
            $return.= '<td>' . $argument->in . '</td>';
            $return.= '</tr>';
        }

        $return.= '</tbody>';
        $return.= '</table>';
        $return.= "\n";

        $return.= "\n";
        $return.= '## Response' . "\n";
        $return.= "\n";
        $return.= '<table>';
        $return.= '<colgroup>';
        $return.= '<col width="40%" />';
        $return.= '<col width="60%" />';
        $return.= '</colgroup>';
        $return.= '<thead>';
        $return.= '<tr>';
        $return.= '<th>Status-Code</th>';
        $return.= '<th>Type</th>';
        $return.= '</tr>';
        $return.= '</thead>';
        $return.= '<tbody>';

        if ($operation->return) {
            $return.= '<tr>';
            $return.= '<td>' . $operation->return->code . '</td>';
            $return.= '<td>' . $operation->return->schema->type . '</td>';
            $return.= '</tr>';
        }

        foreach ($operation->throws as $response) {
            $return.= '<tr>';
            $return.= '<td>' . $response->code . '</td>';
            $return.= '<td>' . $response->schema->type . '</td>';
            $return.= '</tr>';
        }

        $return.= '</tbody>';
        $return.= '</table>';
        $return.= "\n";

        return $return;
    }

    protected function newSchemaGenerator(): SchemaGeneratorInterface
    {
        $config = new Generator\Config();
        $config->put('heading', 4);

        return new Generator\Html($config);
    }
}
