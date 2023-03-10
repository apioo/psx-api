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

namespace PSX\Api\Scanner;

use PSX\Api\Exception\ParserException;
use PSX\Api\Parser;
use PSX\Api\ScannerInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Schema\SchemaManager;
use PSX\Schema\SchemaManagerInterface;

/**
 * The service container scanner can be used to read all annotations from service classes. It can be used i.e. with a DI
 * container like the symfony DI where you can pass all services via a specific tag
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ServiceContainer implements ScannerInterface
{
    private iterable $services;
    private SchemaManagerInterface $schemaManager;
    private Parser\Attribute $parser;

    public function __construct(iterable $services)
    {
        $this->services = $services;
        $this->schemaManager = new SchemaManager();
        $this->parser = new Parser\Attribute($this->schemaManager);
    }

    public function generate(?FilterInterface $filter = null): SpecificationInterface
    {
        $return = new Specification();

        $classes = $this->getServiceClasses();
        foreach ($classes as $class) {
            try {
                $spec = $this->parser->parse($class);

                if ($filter !== null) {
                    $spec->getOperations()->filter($filter);
                }

                $return->merge($spec);
            } catch (ParserException $e) {
            }
        }

        return $return;
    }

    private function getServiceClasses(): array
    {
        $result = [];
        foreach ($this->services as $service) {
            $result[] = get_class($service);
        }

        return $result;
    }
}
