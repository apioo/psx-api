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

namespace PSX\Api\Parser;

use PSX\Api\Exception\ParserException;
use PSX\Api\ParserInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Parser\Context\FilesystemContext;
use PSX\Schema\Parser\ContextInterface;
use PSX\Schema\SchemaManagerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * File
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class File implements ParserInterface
{
    private SchemaManagerInterface $schemaManager;

    public function __construct(SchemaManagerInterface $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    public function parse(string $schema, ?ContextInterface $context = null): SpecificationInterface
    {
        if (!is_file($schema) && $context instanceof FilesystemContext) {
            $schema = $context->getBasePath() . '/' . $schema;
        }

        if (!is_file($schema)) {
            throw new ParserException('Provided schema is not a file');
        }

        $basePath  = pathinfo($schema, PATHINFO_DIRNAME);
        $extension = pathinfo($schema, PATHINFO_EXTENSION);
        if (in_array($extension, ['yaml', 'yml'])) {
            $data = json_decode(json_encode(Yaml::parse(file_get_contents($schema))));
        } else {
            $data = json_decode(file_get_contents($schema));
        }

        if (isset($data->paths)) {
            $parser = new OpenAPI($this->schemaManager);
        } elseif (isset($data->operations) || isset($data->definitions)) {
            $parser = new TypeAPI($this->schemaManager);
        } else {
            throw new ParserException('Could not detect schema format of the provided source ' . $schema);
        }

        return $parser->parseObject($data, new FilesystemContext($basePath));
    }
}
