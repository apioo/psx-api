<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Generator\Proxy;

use PSX\Api\GeneratorInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorFactory;
use PSX\Schema\TypeFactory;

/**
 * Schema
 *
 * @see     https://sdkgen.app/
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Schema implements GeneratorInterface
{
    private string $type;
    private ?Generator\Config $config;
    private GeneratorFactory $factory;

    public function __construct(string $type, ?Generator\Config $config = null)
    {
        $this->type = $type;
        $this->config = $config;
        $this->factory = new GeneratorFactory();
    }

    public function generate(SpecificationInterface $specification): Generator\Code\Chunks|string
    {
        $schema = new \PSX\Schema\Schema(TypeFactory::getAny(), $specification->getDefinitions());
        $generator = $this->factory->getGenerator($this->type, $this->config);

        $result = $generator->generate($schema);
        if ($result instanceof Generator\Code\Chunks && $generator instanceof Generator\FileAwareInterface) {
            $chunks = new Generator\Code\Chunks();
            foreach ($result->getChunks() as $identifier => $content) {
                $chunks->append($generator->getFileName($identifier), $generator->getFileContent($content));
            }

            return $chunks;
        } else {
            return $result;
        }
    }
}
