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

use PSX\Api\Resource;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface;

/**
 * Html
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Html extends MarkupAbstract
{
    public function __construct(?GeneratorInterface $generator = null)
    {
        $this->generator = $generator === null ? new Generator\Html(4) : $generator;
    }

    protected function startResource(Resource $resource): string
    {
        $html = '<div class="psx-resource" data-status="' . $resource->getStatus() . '" data-path="' . $resource->getPath() . '">';
        $html.= '<h1 class="psx-resource-path">' . $resource->getPath() . '</h1>';

        $description = $resource->getDescription();
        if (!empty($description)) {
            $html.= '<div class="psx-resource-description">' . htmlspecialchars($description) . '</div>';
        }

        $html.= '<table>';
        $html.= '<colgroup>';
        $html.= '<col width="30%" />';
        $html.= '<col width="70%" />';
        $html.= '</colgroup>';
        return $html;
    }

    protected function endResource(): string
    {
        $html = '</table>';
        $html.= '</div>';
        return $html;
    }

    protected function startMethod(Resource\MethodAbstract $method): string
    {
        $html = '<tr>';
        $html.= '<td colspan="2" class="psx-resource-method">';
        $html.= '<h2 class="psx-resource-method-name">' . $method->getName() . '</h2>';

        $tags = $method->getTags();
        if (!empty($tags)) {
            $list = [];
            foreach ($tags as $tag) {
                $list[] = '<span class="psx-resource-method-tag">' . htmlspecialchars($tag) . '</span>';
            }

            $html.= '<span class="psx-resource-method-tags">' . implode('', $list) . '</span>';
        }

        $html.= '</td>';
        $html.= '</tr>';
        $html.= '<tr>';
        $html.= '<td colspan="2">';

        $description = $method->getDescription();
        if (!empty($description)) {
            $html.= '<small class="psx-resource-method-description">' . htmlspecialchars($description) . '</small>';
        }

        $html.= '</td>';
        $html.= '</tr>';

        return $html;
    }

    protected function endMethod(): string
    {
        return '';
    }

    protected function renderSchema(string $title, string $schema): string
    {
        $html = '<tr>';
        $html.= '<td><span class="psx-property-name">' . $title . '</span></td>';
        $html.= '<td><a data-name="' . $schema . '" class="psx-type-link">' . $schema . '</a></td>';
        $html.= '</tr>';
        return $html;
    }

    protected function renderMeta(string $title, string $value): string
    {
        $html = '<tr>';
        $html.= '<td><span class="psx-property-name">' . $title . '</span></td>';
        $html.= '<td>' . htmlspecialchars($value) . '</td>';
        $html.= '</tr>';
        return $html;
    }
}
