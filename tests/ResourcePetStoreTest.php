<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Tests;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Api\Generator;
use PSX\Api\Parser;
use PSX\Schema\Parser\JsonSchema\RefResolver;
use Symfony\Component\Yaml\Yaml;

/**
 * ResourceConversionTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceConversionTest extends ApiManagerTestCase
{
    public function testHtml()
    {
        $resources = $this->getResources();
        $generator = new Generator\Html();

        $actual = $generator->generate($resources['/pets']);
        $expect = file_get_contents(__DIR__ . '/Resource/petstore/html.htm');

        $this->assertXmlStringEqualsXmlString('<div>' . $expect . '</div>', '<div>' . $actual . '</div>', $actual);
    }

    public function testMarkdown()
    {
        $resources = $this->getResources();
        $generator = new Generator\Markdown();

        $actual = $generator->generate($resources['/pets']);

        $expect = file_get_contents(__DIR__ . '/Resource/petstore/markdown.md');
        $expect = str_replace(array("\r\n", "\r"), "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testOpenAPI()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');
        $reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $resources = $this->getResources();
        $generator = new Generator\OpenAPI($reader, 1, '/', 'urn:schema.phpsx.org#');

        $actual = $generator->generate($resources['/pets']);
        $expect = file_get_contents(__DIR__ . '/Resource/petstore/openapi.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testPhp()
    {
        $resources = $this->getResources();
        $generator = new Generator\Php();

        $actual = $generator->generate($resources['/pets']);

        $expect = file_get_contents(__DIR__ . '/Resource/petstore/php.php');
        $expect = str_replace(array("\r\n", "\r"), "\n", $expect);

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testRaml()
    {
        $resources = $this->getResources();
        $generator = new Generator\Raml(1, 'http://api.phpsx.org', 'urn:schema.phpsx.org#');

        $actual = $generator->generate($resources['/pets']);
        $actual = json_encode(Yaml::parse($actual));

        $expect = file_get_contents(__DIR__ . '/Resource/petstore/raml.yaml');
        $expect = json_encode(Yaml::parse($expect));

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testSwagger()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');
        $reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $resources = $this->getResources();
        $generator = new Generator\Swagger($reader, 1, 'http://api.phpsx.org', 'urn:schema.phpsx.org#');

        $actual = $generator->generate($resources['/pets']);
        $expect = file_get_contents(__DIR__ . '/Resource/petstore/swagger.json');

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    private function getResources()
    {
        $resolver = new RefResolver();
        $parser   = new Parser\OpenAPI(null, $resolver);

        return $parser->parseAll(json_encode(Yaml::parse($this->getOpenAPI())));
    }

    private function getOpenAPI()
    {
        return <<<'YAML'
openapi: "3.0.0"
info:
  version: 1.0.0
  title: Swagger Petstore
  license:
    name: MIT
servers:
  - url: http://petstore.swagger.io/v1
paths:
  /pets:
    description: foobar
    get:
      summary: List all pets
      operationId: listPets
      tags:
        - pets
      parameters:
        - name: limit
          in: query
          description: How many items to return at one time (max 100)
          required: false
          schema:
            type: integer
            format: int32
      responses:
        200:
          description: An paged array of pets
          headers:
            x-next:
              description: A link to the next page of responses
              schema:
                type: string
          content:
            application/json:    
              schema:
                $ref: "#/components/schemas/Pets"
        500:
          description: unexpected error
          content:
            application/json:
              schema:
                $ref: "#/components/schemas/Error"
    post:
      summary: Create a pet
      operationId: createPets
      tags:
        - pets
      requestBody:
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/Pet"
      responses:
        201:
          description: Null response
        500:
          description: unexpected error
          content:
            application/json:
              schema:
                $ref: "#/components/schemas/Error"
  /pets/{petId}:
    get:
      summary: Info for a specific pet
      operationId: showPetById
      tags:
        - pets
      parameters:
        - name: petId
          in: path
          required: true
          description: The id of the pet to retrieve
          schema:
            type: string
      responses:
        200:
          description: Expected response to a valid request
          content:
            application/json:
              schema:
                $ref: "#/components/schemas/Pets"
        500:
          description: unexpected error
          content:
            application/json:
              schema:
                $ref: "#/components/schemas/Error"
components:
  schemas:
    Pet:
      title: Pet
      required:
        - id
        - name
      properties:
        id:
          type: integer
          format: int64
        name:
          type: string
        tag:
          type: string
    Pets:
      title: Pets
      properties:
        pets:
          type: array
          items:
            $ref: "#/components/schemas/Pet"
    Error:
      title: Error
      required:
        - code
        - message
      properties:
        code:
          type: integer
          format: int32
        message:
          type: string
YAML;
    }
}
