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

namespace PSX\Api\Generator\Markup;

use PSX\Api\GeneratorInterface;
use PSX\Api\Resource;
use PSX\Api\SpecificationInterface;
use PSX\Http\Http;
use PSX\Schema\Definitions;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Schema;
use PSX\Schema\TypeFactory;

/**
 * MarkupAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class MarkupAbstract implements GeneratorInterface
{
    /**
     * @var \PSX\Schema\GeneratorInterface
     */
    protected $generator;

    /**
     * @inheritDoc
     */
    public function generate(SpecificationInterface $specification)
    {
        $collection = $specification->getResourceCollection();
        $definitions = $specification->getDefinitions();

        $text = '';
        foreach ($collection as $path => $resource) {
            $text.= $this->generateResource($resource, new Definitions()) . "\n\n";
        }

        // generate schemas
        $schema = new Schema(TypeFactory::getAny(), $definitions);
        $text.= $this->generator->generate($schema);

        return $text;
    }

    /**
     * @param Resource $resource
     * @param DefinitionsInterface $definitions
     * @return string
     */
    public function generateResource(Resource $resource, DefinitionsInterface $definitions): string
    {
        $text = $this->startResource($resource);

        // path parameters
        $pathParameters = $resource->getPathParameters();
        if (!empty($pathParameters)) {
            $text.= $this->renderSchema('Path-Parameters', $pathParameters);
        }

        $methods = $resource->getMethods();
        foreach ($methods as $method) {
            $text.= $this->startMethod($method);

            // query parameters
            $queryParameters = $method->getQueryParameters();
            if (!empty($queryParameters)) {
                $text.= $this->renderSchema('Query-Parameters', $queryParameters);
            }

            // request
            $request = $method->getRequest();
            if (!empty($request)) {
                $text.= $this->renderSchema('Request', $request);
            }

            // responses
            $responses = $method->getResponses();
            foreach ($responses as $statusCode => $response) {
                $message = isset(Http::$codes[$statusCode]) ? Http::$codes[$statusCode] : 'Unknown';

                $text.= $this->renderSchema('Response - ' . $statusCode . ' ' . $message, $response);
            }

            $text.= $this->endMethod();
        }

        $text.= $this->endResource();

        // generate schemas
        $schema = new Schema(TypeFactory::getAny(), $definitions);
        $text.= $this->generator->generate($schema);

        return $text;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    abstract protected function startResource(Resource $resource);

    /**
     * @return string
     */
    abstract protected function endResource();

    /**
     * @param Resource\MethodAbstract $method
     * @return string
     */
    abstract protected function startMethod(Resource\MethodAbstract $method);

    /**
     * @return string
     */
    abstract protected function endMethod();

    /**
     * @param string $title
     * @param string $schema
     * @return string
     */
    abstract protected function renderSchema(string $title, string $schema);
}
