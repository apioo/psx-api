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
                $this->getMimeForType($type)
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
            GeneratorFactory::TYPE_JSONSCHEMA_ANTHROPIC => 'json',
            GeneratorFactory::TYPE_JSONSCHEMA_GEMINI => 'json',
            GeneratorFactory::TYPE_JSONSCHEMA_OPENAI => 'json',
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

    private function getMimeForType(string $type): string
    {
        return match($type) {
            GeneratorFactory::TYPE_CSHARP => 'application/csharp',
            GeneratorFactory::TYPE_GO => 'application/go',
            GeneratorFactory::TYPE_GRAPHQL => 'application/graphql',
            GeneratorFactory::TYPE_HTML => 'text/html',
            GeneratorFactory::TYPE_JAVA => 'application/java',
            GeneratorFactory::TYPE_JSONSCHEMA => 'application/json',
            GeneratorFactory::TYPE_JSONSCHEMA_ANTHROPIC => 'application/json',
            GeneratorFactory::TYPE_JSONSCHEMA_GEMINI => 'application/json',
            GeneratorFactory::TYPE_JSONSCHEMA_OPENAI => 'application/json',
            GeneratorFactory::TYPE_KOTLIN => 'application/kotlin',
            GeneratorFactory::TYPE_MARKDOWN => 'text/markdown',
            GeneratorFactory::TYPE_PHP => 'application/php',
            GeneratorFactory::TYPE_PROTOBUF => 'application/protobuf',
            GeneratorFactory::TYPE_PYTHON => 'application/python',
            GeneratorFactory::TYPE_RUBY => 'application/ruby',
            GeneratorFactory::TYPE_RUST => 'application/rust',
            GeneratorFactory::TYPE_SWIFT => 'application/swift',
            GeneratorFactory::TYPE_TYPESCRIPT => 'application/typescript',
            GeneratorFactory::TYPE_TYPESCHEMA => 'application/json',
            GeneratorFactory::TYPE_VISUALBASIC => 'application/visualbasic',
            default => 'text/plain',
        };
    }
}
