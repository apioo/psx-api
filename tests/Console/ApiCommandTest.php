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

namespace PSX\Api\Tests\Console;

use PSX\Api\ApiManager;
use PSX\Api\Console\ApiCommand;
use PSX\Api\Tests\Parser\Annotation\TestController;
use PSX\Schema\SchemaManager;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ApiCommandTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratePhp()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'php',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $expect = <<<'TEXT'
<?php

namespace PSX\Generation;

use PSX\Framework\Controller\SchemaApiAbstract;
/**
 * @Title("Test")
 * @Description("Test description")
 * @PathParam(name="fooId", type="string", required=true)
 */
class Endpoint extends SchemaApiAbstract
{
    /**
     * @Description("A long **Test** description")
     * @QueryParam(name="foo", type="string", description="Test")
     * @QueryParam(name="bar", type="string", required=true)
     * @QueryParam(name="baz", type="string", enum={"foo", "bar"})
     * @QueryParam(name="boz", type="string", pattern="[A-z]+")
     * @QueryParam(name="integer", type="integer")
     * @QueryParam(name="number", type="number")
     * @QueryParam(name="date", type="string")
     * @QueryParam(name="boolean", type="boolean")
     * @QueryParam(name="string", type="string")
     * @Incoming(schema="PSX\Generation\ObjectId")
     * @Outgoing(code=200, schema="PSX\Generation\ObjectId")
     */
    public function doGet($record)
    {
    }
}
TEXT;

        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);
        
        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateRaml()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'raml',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $expect = <<<'RAML'
#%RAML 1.0
---
baseUri: 'http://foo.com/'
version: v1
title: psx
/foo:
  description: 'Test description'
  uriParameters:
    fooId:
      type: string
      required: true
  get:
    description: 'A long **Test** description'
    queryParameters:
      foo:
        type: string
        description: Test
        required: false
      bar:
        type: string
        required: true
      baz:
        type: string
        required: false
        enum: [foo, bar]
      boz:
        type: string
        required: false
        pattern: '[A-z]+'
      integer:
        type: integer
        required: false
      number:
        type: number
        required: false
      date:
        type: string
        required: false
      boolean:
        type: boolean
        required: false
      string:
        type: string
        required: false
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:phpsx.org:2016#",
              "type": "object",
              "description": "A canonical song",
              "properties": {
                  "artist": {
                      "type": "string"
                  },
                  "title": {
                      "type": "string"
                  }
              },
              "required": [
                  "title",
                  "artist"
              ]
          }
    responses:
      200:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:phpsx.org:2016#",
                  "type": "object",
                  "description": "A canonical song",
                  "properties": {
                      "artist": {
                          "type": "string"
                      },
                      "title": {
                          "type": "string"
                      }
                  },
                  "required": [
                      "title",
                      "artist"
                  ]
              }

RAML;

        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);
        $expect = str_replace(["\r\n", "\n", "\r"], "\n", $expect);
        
        $this->assertEquals($expect, $actual, $actual);
    }

    public function testGenerateJsonschema()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'jsonschema',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $expect = <<<'JSON'
{
    "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
    "id": "urn:phpsx.org:2016#",
    "definitions": {
        "path-template": {
            "type": "object",
            "title": "path",
            "properties": {
                "fooId": {
                    "type": "string"
                }
            },
            "required": [
                "fooId"
            ]
        },
        "GET-query": {
            "type": "object",
            "title": "query",
            "properties": {
                "foo": {
                    "type": "string",
                    "description": "Test"
                },
                "bar": {
                    "type": "string"
                },
                "baz": {
                    "type": "string",
                    "enum": [
                        "foo",
                        "bar"
                    ]
                },
                "boz": {
                    "type": "string",
                    "pattern": "[A-z]+"
                },
                "integer": {
                    "type": "integer"
                },
                "number": {
                    "type": "number"
                },
                "date": {
                    "type": "string"
                },
                "boolean": {
                    "type": "boolean"
                },
                "string": {
                    "type": "string"
                }
            },
            "required": [
                "bar"
            ]
        },
        "ObjectId": {
            "type": "object",
            "description": "A canonical song",
            "properties": {
                "artist": {
                    "type": "string"
                },
                "title": {
                    "type": "string"
                }
            },
            "required": [
                "title",
                "artist"
            ]
        },
        "GET-request": {
            "$ref": "#\/definitions\/ObjectId"
        },
        "GET-200-response": {
            "$ref": "#\/definitions\/ObjectId"
        }
    }
}
JSON;

        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);
        
        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGenerateSwagger()
    {
        $command = $this->getApiCommand();

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'source' => TestController::class,
            'format' => 'swagger',
            'path'   => '/foo',
        ));

        $actual = $commandTester->getDisplay();
        $expect = <<<'JSON'
{
    "swagger": "2.0",
    "info": {
        "title": "PSX",
        "version": "1"
    },
    "basePath": "\/",
    "paths": {
        "\/foo": {
            "get": {
                "description": "A long **Test** description",
                "operationId": "getObjectId",
                "parameters": [
                    {
                        "description": "Test",
                        "name": "foo",
                        "in": "query",
                        "required": false,
                        "type": "string"
                    },
                    {
                        "name": "bar",
                        "in": "query",
                        "required": true,
                        "type": "string"
                    },
                    {
                        "name": "baz",
                        "in": "query",
                        "required": false,
                        "type": "string",
                        "enum": [
                            "foo",
                            "bar"
                        ]
                    },
                    {
                        "name": "boz",
                        "in": "query",
                        "required": false,
                        "type": "string",
                        "pattern": "[A-z]+"
                    },
                    {
                        "name": "integer",
                        "in": "query",
                        "required": false,
                        "type": "integer"
                    },
                    {
                        "name": "number",
                        "in": "query",
                        "required": false,
                        "type": "number"
                    },
                    {
                        "name": "date",
                        "in": "query",
                        "required": false,
                        "type": "string"
                    },
                    {
                        "name": "boolean",
                        "in": "query",
                        "required": false,
                        "type": "boolean"
                    },
                    {
                        "name": "string",
                        "in": "query",
                        "required": false,
                        "type": "string"
                    },
                    {
                        "description": "A canonical song",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/ObjectId"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "A canonical song",
                        "schema": {
                            "$ref": "#\/definitions\/ObjectId"
                        }
                    }
                }
            },
            "parameters": [
                {
                    "name": "fooId",
                    "in": "path",
                    "required": true,
                    "type": "string"
                }
            ]
        }
    },
    "definitions": {
        "ObjectId": {
            "type": "object",
            "description": "A canonical song",
            "properties": {
                "artist": {
                    "type": "string"
                },
                "title": {
                    "type": "string"
                }
            },
            "required": [
                "title",
                "artist"
            ]
        }
    }
}
JSON;

        $actual = preg_replace('/Object([0-9A-Fa-f]{8})/', 'ObjectId', $actual);

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    protected function getApiCommand()
    {
        $schemaReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $schemaReader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $apiReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $apiReader->addNamespace('PSX\\Api\\Annotation');

        return new ApiCommand(
            new ApiManager(
                $apiReader, 
                new SchemaManager($schemaReader)
            ),
            $schemaReader, 
            'urn:phpsx.org:2016#', 
            'http://foo.com', 
            ''
        );
    }
}
