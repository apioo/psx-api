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

namespace PSX\Api\Parser;

use PSX\Api\Exception\InvalidOperationException;
use PSX\Api\Operation;
use PSX\Api\Operations;
use PSX\Api\OperationsInterface;
use PSX\Api\ParserInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Json\Parser;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Parser as SchemaParser;
use PSX\Schema\TypeFactory;

/**
 * TypeAPI
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TypeAPI implements ParserInterface
{
    private ?string $basePath;
    private SchemaParser\TypeSchema $schemaParser;
    private ?DefinitionsInterface $definitions = null;
    private ?OperationsInterface $operations = null;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath;
        $this->schemaParser = new SchemaParser\TypeSchema(null, $basePath);
    }

    public function parse(string $schema, ?string $path = null): SpecificationInterface
    {
        $data = Parser::decode($schema);

        $schema = $this->schemaParser->parseSchema($data);

        $this->definitions = $schema->getDefinitions();
        $this->operations = new Operations();

        if (isset($data->operations) && $data->operations instanceof \stdClass) {
            $this->parseOperations($data->operations);
        }

        return new Specification($this->operations, $this->definitions);
    }

    /**
     * @throws InvalidOperationException
     */
    private function parseOperations(\stdClass $operations): void
    {
        foreach ($operations as $name => $operation) {
            if ($operation instanceof \stdClass) {
                $this->operations[$name] = $this->parseOperation($operation);
            }
        }
    }

    private function parseOperation(\stdClass $operation): Operation
    {
        $method = $operation->method ?? null;
        $path = $operation->path ?? null;
        $return = $operation->return ?? null;

        $description = $operation->description ?? '';
        $deprecated = $operation->deprecated ?? false;
        $public = $operation->public ?? false;
        $arguments = $operation->arguments ?? null;
        $security = $operation->security ?? null;
        $throws = $operation->throws ?? null;

        if (empty($method) || empty($path)) {
            throw new InvalidOperationException('');
        }

        if (!is_string($method)) {
            throw new InvalidOperationException('');
        }

        if (!is_string($path)) {
            throw new InvalidOperationException('');
        }

        if (!$return instanceof \stdClass) {
            $return = null;
        }

        $result = new Operation($method, $path, $this->parseReturn($return));
        $result->setDescription($description);
        $result->setDeprecated($deprecated);
        $result->setAuthorization($public);

        if ($arguments instanceof \stdClass) {
            $result->setArguments($this->parseArguments($arguments));
        }

        if (is_array($throws)) {
            $result->setThrows($this->parseThrows($throws));
        }

        return $result;
    }

    /**
     * @throws InvalidOperationException
     */
    private function parseReturn(?\stdClass $data): Operation\Response
    {
        if (!$data instanceof \stdClass) {
            return $this->parseResponse($data);
        } else {
            return new Operation\Response(204, TypeFactory::getAny());
        }
    }

    /**
     * @throws InvalidOperationException
     */
    private function parseArguments(\stdClass $data): array
    {
        $return = [];
        foreach ($data as $name => $argument) {
            if ($argument instanceof \stdClass) {
                $return[$name] = $this->parseArgument($argument);
            }
        }

        return $return;
    }

    private function parseArgument(\stdClass $data): Operation\Argument
    {
        $in = $data->in ?? null;
        if (empty($in)) {
            throw new InvalidOperationException('');
        }

        $schema = $data->schema ?? null;
        if (!$schema instanceof \stdClass) {
            throw new InvalidOperationException('');
        }

        return new Operation\Argument($in, $this->schemaParser->parseType($schema));
    }

    /**
     * @throws InvalidOperationException
     */
    private function parseThrows(array $data): array
    {
        $return = [];
        foreach ($data as $throw) {
            if ($throw instanceof \stdClass) {
                $return[] = $this->parseResponse($throw);
            }
        }

        return $return;
    }

    private function parseResponse(\stdClass $data): Operation\Response
    {
        $code = $data->code ?? 200;

        $schema = $data->schema ?? null;
        if (!$schema instanceof \stdClass) {
            throw new InvalidOperationException('');
        }

        return new Operation\Response($code, $this->schemaParser->parseType($schema));
    }
}
