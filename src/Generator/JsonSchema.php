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

use PSX\Api\GeneratorAbstract;
use PSX\Api\Resource;
use PSX\Json\Parser;
use PSX\Schema\Generator;
use PSX\Schema\Schema;
use PSX\Schema\SchemaInterface;

/**
 * JsonSchema
 *
 * @see     http://tools.ietf.org/html/draft-zyp-json-schema-04
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonSchema extends GeneratorAbstract
{
    use Generator\GeneratorTrait;

    /**
     * @var string
     */
    protected $targetNamespace;

    /**
     * @param string $targetNamespace
     */
    public function __construct($targetNamespace)
    {
        $this->targetNamespace = $targetNamespace;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    public function generate(Resource $resource)
    {
        return Parser::encode($this->toArray($resource), JSON_PRETTY_PRINT);
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return array
     */
    public function toArray(Resource $resource)
    {
        $generator = new Generator\JsonSchema($this->targetNamespace);

        $result  = [];
        $refs    = [];
        $methods = $resource->getMethods();

        // path parameters
        if ($resource->hasPathParameters()) {
            $result['path-template'] = new Schema($resource->getPathParameters());
        }

        foreach ($methods as $method) {
            // query parameters
            if ($method->hasQueryParameters()) {
                $result[$method->getName() . '-query'] = new Schema($method->getQueryParameters());
            }

            // request
            $request = $method->getRequest();
            if ($request instanceof SchemaInterface) {
                $name = $this->getIdentifierForProperty($request->getDefinition());
                $refs[$method->getName() . '-request'] = (object) ['$ref' => '#/definitions/' . $name];

                $result[$name] = $request;
            }

            // response
            $responses = $method->getResponses();
            foreach ($responses as $statusCode => $response) {
                if ($response instanceof SchemaInterface) {
                    $name = $this->getIdentifierForProperty($response->getDefinition());
                    $refs[$method->getName() . '-' . $statusCode . '-response'] = (object) [
                        '$ref' => '#/definitions/' . $name,
                    ];

                    $result[$name] = $response;
                }
            }
        }

        $definitions = new \stdClass();
        foreach ($result as $name => $property) {
            $schema = $generator->toArray($property);

            // @TODO if a property contains a self reference i.e. # we should
            // adjust the reference to the concrete definition since after the
            // merge # is not valid anymore

            if (isset($schema['definitions'])) {
                foreach ($schema['definitions'] as $defName => $definition) {
                    $definitions->{$defName} = $definition;
                }

                unset($schema['definitions']);
            }

            if (isset($schema['$schema'])) {
                unset($schema['$schema']);
            }

            if (isset($schema['id'])) {
                unset($schema['id']);
            }

            $definitions->{$name} = $schema;
        }

        foreach ($refs as $name => $ref) {
            $definitions->{$name} = $ref;
        }

        return array(
            '$schema'     => Generator\JsonSchema::SCHEMA,
            'id'          => $this->targetNamespace,
            'definitions' => $definitions,
        );
    }
}
