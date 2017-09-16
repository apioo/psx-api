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
use PSX\Http\Http;
use PSX\Schema\Generator\GeneratorTrait;
use PSX\Schema\PropertyInterface;
use PSX\Schema\SchemaInterface;

/**
 * MarkupAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class MarkupAbstract implements GeneratorInterface
{
    use GeneratorTrait;

    const TYPE_PATH     = 0x1;
    const TYPE_QUERY    = 0x2;
    const TYPE_REQUEST  = 0x3;
    const TYPE_RESPONSE = 0x4;

    /**
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    public function generate(Resource $resource)
    {
        $text = $this->startResource($resource);

        // path parameters
        $pathParameters = $resource->getPathParameters();
        if ($pathParameters instanceof PropertyInterface && $pathParameters->hasConstraints()) {
            $result = $this->getParameters($pathParameters, self::TYPE_PATH, $resource->getPath());

            if (!empty($result)) {
                $text.= $this->startParameters('Path-Parameters', self::TYPE_PATH);
                $text.= $result;
                $text.= $this->endParameters();
            }
        }

        $methods = $resource->getMethods();
        foreach ($methods as $method) {
            $text.= $this->startMethod($method);

            // query parameters
            $queryParameters = $method->getQueryParameters();
            if ($queryParameters instanceof PropertyInterface && $queryParameters->hasConstraints()) {
                $result = $this->getParameters($queryParameters, self::TYPE_QUERY, $resource->getPath(), $method->getName());

                if (!empty($result)) {
                    $text.= $this->startParameters($method->getName() . ' Query-Parameters', self::TYPE_QUERY);
                    $text.= $result;
                    $text.= $this->endParameters();
                }
            }

            // request
            $request = $method->getRequest();
            if ($request instanceof SchemaInterface) {
                $result = $this->getSchema($request, self::TYPE_REQUEST, $resource->getPath(), $method->getName());

                if (!empty($result)) {
                    $text.= $this->startSchema($method->getName() . ' Request', self::TYPE_REQUEST);
                    $text.= $result;
                    $text.= $this->endSchema();
                }
            }

            // responses
            $responses = $method->getResponses();
            foreach ($responses as $statusCode => $response) {
                $result = $this->getSchema($response, self::TYPE_RESPONSE, $resource->getPath(), $method->getName(), $statusCode);

                if (!empty($result)) {
                    $message = isset(Http::$codes[$statusCode]) ? Http::$codes[$statusCode] : 'Unknown';

                    $text.= $this->startSchema($method->getName() . ' Response - ' . $statusCode . ' ' . $message, self::TYPE_RESPONSE);
                    $text.= $result;
                    $text.= $this->endSchema();
                }
            }

            $text.= $this->endMethod();
        }

        $text.= $this->endResource();

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
     * @param integer $type
     * @return string
     */
    abstract protected function startParameters($title, $type);

    /**
     * @return string
     */
    abstract protected function endParameters();

    /**
     * @param \PSX\Schema\PropertyInterface $property
     * @param integer $type
     * @param string $path
     * @param string|null $method
     * @return string
     */
    abstract protected function getParameters(PropertyInterface $property, $type, $path, $method = null);

    /**
     * @param string $title
     * @param integer $type
     * @return string
     */
    abstract protected function startSchema($title, $type);

    /**
     * @return string
     */
    abstract protected function endSchema();

    /**
     * @param SchemaInterface $schema
     * @param integer $type
     * @param string $path
     * @param string $method
     * @param integer|null $statusCode
     * @return string
     */
    abstract protected function getSchema(SchemaInterface $schema, $type, $path, $method, $statusCode = null);
}
