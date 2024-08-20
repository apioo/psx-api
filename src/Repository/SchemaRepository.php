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

namespace PSX\Api\Repository;

use PSX\Api\Generator;
use PSX\Schema\Generator\Config;
use PSX\Schema\GeneratorFactory;

/**
 * SchemaRepository
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class SchemaRepository implements RepositoryInterface
{
    public function getAll(): array
    {
        $result = [];

        $types = GeneratorFactory::getPossibleTypes();
        foreach ($types as $type) {
            $result['model-' . $type] = new GeneratorConfig(
                fn(?string $baseUrl, ?Config $config) => new Generator\Proxy\Schema($type, $config),
                $this->getFileExtensionForType($type),
                'text/plain'
            );
        }

        return $result;
    }

    private function getFileExtensionForType(string $type): string
    {
        return match($type) {
            GeneratorFactory::TYPE_CSHARP => 'cs',
            GeneratorFactory::TYPE_GO => 'go',
            GeneratorFactory::TYPE_GRAPHQL => 'graphql',
            GeneratorFactory::TYPE_HTML => 'html',
            GeneratorFactory::TYPE_JAVA => 'java',
            GeneratorFactory::TYPE_JSONSCHEMA => 'json',
            GeneratorFactory::TYPE_KOTLIN => 'kt',
            GeneratorFactory::TYPE_MARKDOWN => 'md',
            GeneratorFactory::TYPE_PHP => 'php',
            GeneratorFactory::TYPE_PROTOBUF => 'proto',
            GeneratorFactory::TYPE_PYTHON => 'py',
            GeneratorFactory::TYPE_RUBY => 'rb',
            GeneratorFactory::TYPE_RUST => 'rt',
            GeneratorFactory::TYPE_SWIFT => 'swift',
            GeneratorFactory::TYPE_TYPESCRIPT => 'ts',
            GeneratorFactory::TYPE_TYPESCHEMA => 'json',
            GeneratorFactory::TYPE_VISUALBASIC => 'vb',
            default => 'txt',
        };
    }
}
