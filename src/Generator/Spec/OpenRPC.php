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

namespace PSX\Api\Generator\Spec;

use PSX\Api\Generator\ConfigurationAwareInterface;
use PSX\Api\Generator\ConfigurationTrait;
use PSX\Api\OperationInterface;
use PSX\Api\SpecificationInterface;
use PSX\Json\Parser;
use PSX\OpenAPI\Contact;
use PSX\OpenAPI\Info;
use PSX\OpenAPI\License;
use PSX\OpenAPI\Schemas;
use PSX\OpenAPI\Server;
use PSX\OpenRPC\Components;
use PSX\OpenRPC\ContentDescriptor;
use PSX\OpenRPC\Method;
use PSX\OpenRPC\OpenRPC as Declaration;
use PSX\Schema\ContentType;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\Parser\Popo\Dumper;
use PSX\Schema\Type\PropertyTypeAbstract;

/**
 * Generates an OpenAPI 3.0 representation of an API resource
 *
 * @see     https://www.openapis.org/
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OpenRPC extends ApiAbstract implements ConfigurationAwareInterface
{
    use ConfigurationTrait;

    private int $apiVersion;

    private Dumper $dumper;
    private Generator\JsonSchema $generator;

    public function __construct(int $apiVersion, ?string $baseUrl)
    {
        $this->apiVersion = $apiVersion;
        $this->baseUrl = $baseUrl;
        $this->dumper = new Dumper();
        $this->generator = new Generator\JsonSchema($this->newConfig());
    }

    public function generate(SpecificationInterface $specification): Generator\Code\Chunks|string
    {
        $operations = $specification->getOperations();
        $definitions = $specification->getDefinitions();

        $methods = [];
        foreach ($operations->getAll() as $operationId => $operation) {
            $methods[] = $this->buildMethod($operationId, $operation, $definitions);
        }

        return $this->buildDeclaration($methods, $definitions, $this->getBaseUrl($specification));
    }

    protected function buildDeclaration(array $methods, DefinitionsInterface $definitions, ?string $baseUrl): string
    {
        $info = new Info();
        $info->setTitle($this->title ?: 'PSX');
        $info->setDescription($this->description);
        $info->setTermsOfService($this->tos);

        if (!empty($this->contactName)) {
            $contact = new Contact();
            $contact->setName($this->contactName);
            $contact->setUrl($this->contactUrl);
            $contact->setEmail($this->contactEmail);

            $info->setContact($contact);
        }

        if (!empty($this->licenseName)) {
            $license = new License();
            $license->setName($this->licenseName);
            $license->setUrl($this->licenseUrl);

            $info->setLicense($license);
        }

        $info->setVersion((string) $this->apiVersion);

        $server = new Server();
        if (!empty($baseUrl)) {
            $server->setUrl(rtrim($baseUrl, '/'));
        }

        $result = $this->generator->toArray($definitions, null);

        $schemas = new Schemas();
        foreach ($result['definitions'] ?? [] as $name => $schema) {
            $schemas[$name] = $schema;
        }

        $components = new Components();
        $components->setSchemas($schemas);

        $declaration = new Declaration();
        $declaration->setInfo($info);
        $declaration->setServers([$server]);
        $declaration->setMethods($methods);
        $declaration->setComponents($components);

        $data = $this->dumper->dump($declaration);
        $data = Parser::encode($data);

        return $data;
    }

    protected function buildMethod(string $operationId, OperationInterface $operation, DefinitionsInterface $definitions): Method
    {
        $method = new Method();
        $method->setName($operationId);
        $method->setDescription($operation->getDescription());

        $params = [];
        foreach ($operation->getArguments()->getAll() as $argumentName => $argument) {
            $params[] = $this->newDescriptor($argumentName, $argument->getSchema(), $definitions);
        }

        $method->setParams($params);
        $method->setResult($this->newDescriptor('result', $operation->getReturn()->getSchema(), $definitions));

        return $method;
    }

    protected function newDescriptor(string $name, PropertyTypeAbstract|ContentType $type, DefinitionsInterface $definitions): ContentDescriptor
    {
        if ($type instanceof ContentType) {
            if ($type->getShape() === ContentType::JSON) {
                $schema = (object) [
                    'type' => 'object',
                    'additionalProperties' => true,
                ];
            } else {
                $schema = (object) [
                    'type' => 'string',
                ];

                if ($type->getShape() === ContentType::BINARY) {
                    $schema->format = 'binary';
                }
            }
        } else {
            $schema = $this->generator->toProperty($type, $definitions);
        }

        $descriptor = new ContentDescriptor();
        $descriptor->setName($name);
        $descriptor->setSchema($schema);

        return $descriptor;
    }

    private function newConfig(): Generator\Config
    {
        $config = new Generator\Config();
        $config->put('ref_base', '#/components/schemas/');

        return $config;
    }
}
