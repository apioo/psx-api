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

use PSX\Api\Attribute\Security;
use PSX\Api\Exception\InvalidOperationException;
use PSX\Api\Exception\ParserException;
use PSX\Api\Operation;
use PSX\Api\Operations;
use PSX\Api\OperationsInterface;
use PSX\Api\ParserInterface;
use PSX\Api\SecurityInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Json\Parser;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Exception\InvalidSchemaException;
use PSX\Schema\Parser as SchemaParser;
use PSX\Schema\TypeFactory;
use Symfony\Component\Yaml\Yaml;

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

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath;
        $this->schemaParser = new SchemaParser\TypeSchema(null, $basePath);
    }

    /**
     * @throws ParserException
     */
    public function parse(string $schema): SpecificationInterface
    {
        try {
            $data = Parser::decode($schema);
            if (!$data instanceof \stdClass) {
                throw new ParserException('Provided schema must be an object');
            }

            return $this->parseObject($data);
        } catch (\JsonException $e) {
            throw new ParserException('Could not parse JSON: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws ParserException
     */
    public function parseObject(\stdClass $data): SpecificationInterface
    {
        try {
            $schema = $this->schemaParser->parseSchema($data);

            $definitions = $schema->getDefinitions();

            $operations = new Operations();
            if (isset($data->operations) && $data->operations instanceof \stdClass) {
                $operations = $this->parseOperations($data->operations);
            }

            $security = null;
            if (isset($data->security) && $data->security instanceof \stdClass) {
                $security = $this->parseSecurity($data->security);
            }

            return new Specification($operations, $definitions, $security);
        } catch (\Throwable $e) {
            throw new ParserException('An error occurred while parsing: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws ParserException
     * @throws InvalidSchemaException
     */
    private function parseOperations(\stdClass $operations): OperationsInterface
    {
        $return = new Operations();
        foreach ($operations as $name => $operation) {
            if ($operation instanceof \stdClass) {
                $return->add($name, $this->parseOperation($operation));
            }
        }
        return $return;
    }

    /**
     * @throws ParserException
     * @throws InvalidSchemaException
     */
    private function parseOperation(\stdClass $operation): Operation
    {
        $method = $operation->method ?? null;
        $path = $operation->path ?? null;
        $return = $operation->return ?? null;

        if (empty($method) || !is_string($method)) {
            throw new ParserException('Property "method" must be a string and not empty');
        }

        if (empty($path) || !is_string($path)) {
            throw new ParserException('Property "path" must be a string and not empty');
        }

        if (!$return instanceof \stdClass) {
            $return = null;
        }

        $result = new Operation($method, $path, $this->parseReturn($return));

        if (isset($operation->arguments) && $operation->arguments instanceof \stdClass) {
            $result->setArguments($this->parseArguments($operation->arguments));
        }

        if (is_array($operation->throws ?? null)) {
            $result->setThrows($this->parseThrows($operation->throws));
        }

        if (is_string($operation->description ?? null)) {
            $result->setDescription($operation->description);
        }

        if (is_bool($operation->deprecated ?? null)) {
            $result->setDeprecated($operation->deprecated);
        }

        if (is_array($operation->security ?? null)) {
            $result->setSecurity($operation->security);
        }

        if (is_bool($operation->authorization ?? null)) {
            $result->setAuthorization($operation->authorization);
        }

        if (is_array($operation->tags ?? null)) {
            $result->setTags($operation->tags);
        }

        return $result;
    }

    /**
     * @throws ParserException
     * @throws InvalidSchemaException
     */
    private function parseReturn(?\stdClass $data): Operation\Response
    {
        if ($data instanceof \stdClass) {
            return $this->parseResponse($data);
        } else {
            return new Operation\Response(204, TypeFactory::getAny());
        }
    }

    /**
     * @throws ParserException
     * @throws InvalidSchemaException
     */
    private function parseArguments(\stdClass $data): Operation\Arguments
    {
        $return = new Operation\Arguments();
        foreach ($data as $name => $argument) {
            if ($argument instanceof \stdClass) {
                $return->add($name, $this->parseArgument($argument));
            }
        }

        return $return;
    }

    /**
     * @throws ParserException
     * @throws InvalidSchemaException
     */
    private function parseArgument(\stdClass $data): Operation\Argument
    {
        $in = $data->in ?? null;
        if (empty($in) || !is_string($in)) {
            throw new ParserException('Property "in" must be a string and not empty');
        }

        $schema = $data->schema ?? null;
        if (!$schema instanceof \stdClass) {
            throw new ParserException('Property "schema" must be an object');
        }

        return new Operation\Argument($in, $this->schemaParser->parseType($schema));
    }

    /**
     * @throws ParserException
     * @throws InvalidSchemaException
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

    /**
     * @throws ParserException
     * @throws InvalidSchemaException
     */
    private function parseResponse(\stdClass $data): Operation\Response
    {
        $code = $data->code ?? 200;
        if (empty($code) || !is_int($code)) {
            throw new ParserException('Property "code" must be an int and not empty');
        }

        $schema = $data->schema ?? null;
        if (!$schema instanceof \stdClass) {
            throw new ParserException('Property "schema" must be an object');
        }

        return new Operation\Response($code, $this->schemaParser->parseType($schema));
    }

    private function parseSecurity(\stdClass $data): ?SecurityInterface
    {
        $type = $data->type ?? null;
        if (!is_string($type)) {
            return null;
        }

        $type = strtolower($type);
        if ($type === 'http') {
            $scheme = $data->scheme ?? null;

        }
        switch (strtolower($type)) {
            case 'http':
            case 'apikey':
            case 'oauth2':
                break;

        }

        return new Security();
    }

    public static function fromFile(string $file): SpecificationInterface
    {
        if (empty($file) || !is_file($file)) {
            throw new ParserException('Could not load TypeAPI schema ' . $file);
        }

        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array($extension, ['yaml', 'yml'])) {
            $data = json_encode(Yaml::parse(file_get_contents($file)));
        } else {
            $data = file_get_contents($file);
        }

        $basePath = pathinfo($file, PATHINFO_DIRNAME);
        $parser   = new TypeAPI($basePath);

        return $parser->parse($data);
    }
}
