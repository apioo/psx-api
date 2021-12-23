<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Transformer;

use PSX\Schema\Transformer\JsonSchema;

/**
 * Converts all schema aspects of an OpenAPI specification to TypeSchema
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OpenAPI
{
    private JsonSchema $transformer;

    public function __construct()
    {
        $this->transformer = new JsonSchema();
    }

    /**
     * Transform the provided OpenAPI spec, note we execute this transformation directly on the provided object so the
     * provided schema will change after calling this method, this is done to handle also large OpenAPI specs
     */
    public function transform(\stdClass $schema): \stdClass
    {
        $this->transformOperations($schema);
        $this->transformSchemas($schema);

        return $schema;
    }

    private function transformOperations(\stdClass $spec): void
    {
        $paths = $spec->paths ?? null;
        if (!$paths instanceof \stdClass) {
            return;
        }

        foreach ($paths as $methods) {
            if (!$methods instanceof \stdClass) {
                continue;
            }

            foreach ($methods as $operation) {
                $this->transformOperation($operation, $spec);
            }
        }
    }

    public function transformOperation(\stdClass $operation, \stdClass $spec): void
    {
        $responses = $operation->responses ?? null;
        if ($responses instanceof \stdClass) {
            foreach ($responses as $statusCode => $response) {
                $schema = $response->content->{'application/json'}->schema ?? null;
                if ($schema instanceof \stdClass) {
                    $result = $this->transformer->transform($schema);

                    $response->content->{'application/json'}->schema = ['$ref' => $result->{'$ref'}];

                    foreach ($result->definitions as $name => $type) {
                        $spec->components->schemas->{$name} = $type;
                    }
                }
            }
        }
    }

    private function transformSchemas(\stdClass $spec): void
    {
        $schemas = $spec->components->schemas ?? null;
        if (!$schemas instanceof \stdClass) {
            return;
        }

        $result = $this->transformer->transform((object) ['definitions' => $schemas]);

        $spec->components->schemas = new \stdClass();
        foreach ($result->definitions as $name => $type) {
            $spec->components->schemas->{$name} = $type;
        }
    }
}
