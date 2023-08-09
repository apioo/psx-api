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

namespace PSX\Api\Parser;

use PSX\Api\Exception\InvalidArgumentException;
use PSX\Api\Exception\ParserException;
use PSX\Api\Operation;
use PSX\Api\Operations;
use PSX\Api\OperationsInterface;
use PSX\Api\ParserInterface;
use PSX\Api\Security;
use PSX\Api\SecurityInterface;
use PSX\Api\Specification;
use PSX\Api\SpecificationInterface;
use PSX\Json\Parser;
use PSX\Schema\Exception\InvalidSchemaException;
use PSX\Schema\Parser as SchemaParser;
use PSX\Schema\Parser\ContextInterface;
use PSX\Schema\SchemaManagerInterface;
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
    private SchemaParser\TypeSchema $schemaParser;

    public function __construct(SchemaManagerInterface $schemaManager)
    {
        $this->schemaParser = new SchemaParser\TypeSchema($schemaManager);
    }

    /**
     * @throws ParserException
     */
    public function parse(string $schema, ?ContextInterface $context = null): SpecificationInterface
    {
        try {
            $data = Parser::decode($schema);
            if (!$data instanceof \stdClass) {
                throw new ParserException('Provided schema must be an object');
            }

            return $this->parseObject($data, $context);
        } catch (\JsonException $e) {
            throw new ParserException('Could not parse JSON: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws ParserException
     */
    public function parseObject(\stdClass $data, ?ContextInterface $context = null): SpecificationInterface
    {
        try {
            $schema = $this->schemaParser->parseSchema($data, $context);

            $definitions = $schema->getDefinitions();

            $operations = new Operations();
            if (isset($data->operations) && $data->operations instanceof \stdClass) {
                $operations = $this->parseOperations($data->operations);
            }

            $security = null;
            if (isset($data->security) && $data->security instanceof \stdClass) {
                $security = $this->parseSecurity($data->security);
            }

            $baseUrl = null;
            if (isset($data->baseUrl) && is_string($data->baseUrl)) {
                $baseUrl = $data->baseUrl;
            }

            return new Specification($operations, $definitions, $security, $baseUrl);
        } catch (\Throwable $e) {
            throw new ParserException('An error occurred while parsing: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws ParserException
     * @throws InvalidSchemaException
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
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

        if (is_int($operation->stability ?? null)) {
            $result->setStability($operation->stability);
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
     * @throws InvalidArgumentException
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

        $name = $data->name ?? null;
        if ($name !== null && !is_string($name)) {
            throw new ParserException('Property "name" must be a string');
        }

        return new Operation\Argument($in, $this->schemaParser->parseType($schema), $name);
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

    /**
     * @throws ParserException
     */
    private function parseSecurity(\stdClass $data): ?SecurityInterface
    {
        $type = $data->type ?? null;
        if (!is_string($type)) {
            return null;
        }

        switch ($type) {
            case SecurityInterface::TYPE_HTTP_BASIC:
                return new Security\HttpBasic();
            case SecurityInterface::TYPE_HTTP_BEARER:
                return new Security\HttpBearer();
            case SecurityInterface::TYPE_API_KEY:
                $name = isset($data->name) && is_string($data->name) ? $data->name : throw new ParserException('Provided security "apiKey" must contain a "name" property');
                $in = isset($data->in) && is_string($data->in) ? $data->in : throw new ParserException('Provided security "apiKey" must contain an "in" property');

                return new Security\ApiKey($name, $in);
            case SecurityInterface::TYPE_OAUTH2:
                $tokenUrl = isset($data->tokenUrl) && is_string($data->tokenUrl) ? $data->tokenUrl : throw new ParserException('Provided security "oauth2" must contain a "tokenUrl" property');
                $authorizationUrl = isset($data->authorizationUrl) && is_string($data->authorizationUrl) ? $data->authorizationUrl : null;
                $scopes = isset($data->scopes) && is_string($data->scopes) ? $data->scopes : [];

                return new Security\OAuth2($tokenUrl, $authorizationUrl, $scopes);
        }

        return null;
    }
}
