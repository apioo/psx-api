<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Generator;

use PSX\Api\Resource;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface;
use PSX\Schema\PropertyInterface;
use PSX\Schema\Schema;
use PSX\Schema\SchemaInterface;

/**
 * Html
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Html extends MarkupAbstract
{
    /**
     * @var \PSX\Schema\GeneratorInterface
     */
    protected $generator;

    /**
     * @param \PSX\Schema\GeneratorInterface|null $generator
     */
    public function __construct(GeneratorInterface $generator = null)
    {
        $this->generator = $generator === null ? new Generator\Html(4) : $generator;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    protected function startResource(Resource $resource)
    {
        $html = '<div class="psx-resource" data-status="' . $resource->getStatus() . '" data-path="' . $resource->getPath() . '">';
        $html.= '<h1>' . $resource->getPath() . '</h1>';

        $description = $resource->getDescription();
        if (!empty($description)) {
            $html.= '<div class="psx-resource-description">' . $description . '</div>';
        }

        return $html;
    }

    /**
     * @return string
     */
    protected function endResource()
    {
        $html = '</div>';
        return $html;
    }

    /**
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @return string
     */
    protected function startMethod(Resource\MethodAbstract $method)
    {
        $html = '<div class="psx-resource-method" data-method="' . $method->getName() . '">';
        $html.= '<h2>' . $method->getName() . '</h2>';

        $description = $method->getDescription();
        if (!empty($description)) {
            $html.= '<div class="psx-resource-method-description">' . $description . '</div>';
        }

        return $html;
    }

    /**
     * @return string
     */
    protected function endMethod()
    {
        $html = '</div>';
        return $html;
    }

    /**
     * @param string $title
     * @param integer $type
     * @return string
     */
    protected function startParameters($title, $type)
    {
        $html = '<div class="psx-resource-parameters" data-type="' . $type . '">';
        $html.= '<h3>' . $title . '</h3>';
        $html.= '<div class="psx-resource-parameters-content">';
        return $html;
    }

    /**
     * @return string
     */
    protected function endParameters()
    {
        $html = '</div>';
        $html.= '</div>';
        return $html;
    }

    /**
     * @param \PSX\Schema\PropertyInterface $property
     * @param integer $type
     * @param string $path
     * @param string|null $method
     * @return string
     */
    protected function getParameters(PropertyInterface $property, $type, $path, $method = null)
    {
        return $this->generator->generate(new Schema($property));
    }

    /**
     * @param string $title
     * @param integer $type
     * @return string
     */
    protected function startSchema($title, $type)
    {
        $html = '<div class="psx-resource-schema" data-type="' . $type . '">';
        $html.= '<h3>' . $title . '</h3>';
        $html.= '<div class="psx-resource-schema-content">';
        return $html;
    }

    /**
     * @return string
     */
    protected function endSchema()
    {
        $html = '</div>';
        $html.= '</div>';
        return $html;
    }

    /**
     * @param SchemaInterface $schema
     * @param int $type
     * @param string $path
     * @param string $method
     * @param integer|null $statusCode
     */
    protected function getSchema(SchemaInterface $schema, $type, $path, $method, $statusCode = null)
    {
        return $this->generator->generate($schema);
    }
}
