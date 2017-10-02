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

use PSX\Api\GeneratorInterface;
use PSX\Api\Resource;

/**
 * Template
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Template implements GeneratorInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $engine;

    /**
     * @var string
     */
    protected $template;

    /**
     * @param string $template
     */
    public function __construct($template)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../resources');

        $this->engine   = new \Twig_Environment($loader);
        $this->template = $template;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    public function generate(Resource $resource)
    {
        return $this->engine->render($this->template, ['resource' => $resource]);
    }
}
