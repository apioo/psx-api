<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Api\GeneratorCollectionInterface;
use PSX\Api\GeneratorInterface;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Schema\Generator;
use PSX\Schema\Property;
use PSX\Schema\Schema;
use PSX\Schema\SchemaInterface;

/**
 * Typescript
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Typescript implements GeneratorInterface, GeneratorCollectionInterface
{
    use Generator\GeneratorTrait;

    /**
     * @inheritdoc
     */
    public function generate(Resource $resource)
    {
        $generator = new Generator\Typescript();

        $result  = [];
        $methods = $resource->getMethods();

        // path parameters
        if ($resource->hasPathParameters()) {
            $result['PathTemplate'] = $resource->getPathParameters();
        }

        foreach ($methods as $method) {
            // query parameters
            if ($method->hasQueryParameters()) {
                $result[ucfirst(strtolower($method->getName())) . 'Query'] = $method->getQueryParameters();
            }

            // request
            $request = $method->getRequest();
            if ($request instanceof SchemaInterface) {
                $property = $request->getDefinition();
                $name     = $this->getIdentifierForProperty($property);

                $result[$name] = $property;
            }

            // response
            $responses = $method->getResponses();
            foreach ($responses as $statusCode => $response) {
                if ($response instanceof SchemaInterface) {
                    $property = $response->getDefinition();
                    $name     = $this->getIdentifierForProperty($property);

                    $result[$name] = $property;
                }
            }
        }

        $prop = Property::getObject();
        $prop->setTitle('Endpoint');
        foreach ($result as $name => $property) {
            $prop->addProperty($name, $property);
        }

        $namespace = $this->getClassName($resource->getPath());

        $result = 'namespace ' . $namespace . ' {' . "\n" . '    ';
        $result.= str_replace("\n", "\n    ", $generator->generate(new Schema($prop))) . "\n";
        $result.= '}' . "\n";
        
        $result = str_replace('interface ', 'export interface ', $result);
        
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function generateAll(ResourceCollection $collection)
    {
        $result = '';
        foreach ($collection as $path => $resource) {
            $result.= $this->generate($resource) . "\n";
        }

        return $result;
    }

    private function getClassName($path)
    {
        $parts = explode('/', $path);
        $parts = array_map(function($part){
            return ucfirst(preg_replace('/[^A-Za-z0-9_]+/', '', $part));
        }, $parts);

        return implode('', $parts);
    }
}
