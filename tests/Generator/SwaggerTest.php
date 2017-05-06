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

namespace PSX\Api\Tests\Generator;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Api\Generator\Swagger;

/**
 * SwaggerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SwaggerTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $generator = new Swagger($reader, 1, '/', 'http://foo.phpsx.org');
        $json      = $generator->generate($this->getResource());

        $expect = <<<'JSON'
{
    "swagger": "2.0",
    "info": {
        "title": "PSX",
        "version": "1"
    },
    "basePath": "\/",
    "paths": {
        "\/foo\/{name}\/{type}": {
            "get": {
                "description": "Returns a collection",
                "operationId": "getCollection",
                "parameters": [
                    {
                        "description": "startIndex parameter",
                        "name": "startIndex",
                        "in": "query",
                        "required": true,
                        "type": "integer",
                        "maximum": 32,
                        "minimum": 0
                    },
                    {
                        "name": "float",
                        "in": "query",
                        "required": false,
                        "type": "number"
                    },
                    {
                        "name": "boolean",
                        "in": "query",
                        "required": false,
                        "type": "boolean"
                    },
                    {
                        "name": "date",
                        "in": "query",
                        "required": false,
                        "type": "string",
                        "format": "date"
                    },
                    {
                        "name": "datetime",
                        "in": "query",
                        "required": false,
                        "type": "string",
                        "format": "date-time"
                    }
                ],
                "responses": {
                    "200": {
                        "description": "GET 200 response",
                        "schema": {
                            "$ref": "#\/definitions\/Collection"
                        }
                    }
                }
            },
            "put": {
                "operationId": "putItem",
                "parameters": [
                    {
                        "description": "PUT request",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/Item"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "PUT 200 response",
                        "schema": {
                            "$ref": "#\/definitions\/Message"
                        }
                    }
                }
            },
            "post": {
                "operationId": "postItem",
                "parameters": [
                    {
                        "description": "POST request",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/Item"
                        }
                    }
                ],
                "responses": {
                    "201": {
                        "description": "POST 201 response",
                        "schema": {
                            "$ref": "#\/definitions\/Message"
                        }
                    }
                }
            },
            "delete": {
                "operationId": "deleteItem",
                "parameters": [
                    {
                        "description": "DELETE request",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/Item"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "DELETE 200 response",
                        "schema": {
                            "$ref": "#\/definitions\/Message"
                        }
                    }
                }
            },
            "patch": {
                "operationId": "patchItem",
                "parameters": [
                    {
                        "description": "PATCH request",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/Item"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "PATCH 200 response",
                        "schema": {
                            "$ref": "#\/definitions\/Message"
                        }
                    }
                }
            },
            "parameters": [
                {
                    "description": "Name parameter",
                    "name": "name",
                    "in": "path",
                    "required": true,
                    "type": "string",
                    "maxLength": 16,
                    "minLength": 0,
                    "pattern": "[A-z]+"
                },
                {
                    "name": "type",
                    "in": "path",
                    "required": true,
                    "type": "string",
                    "enum": [
                        "foo",
                        "bar"
                    ]
                }
            ]
        }
    },
    "definitions": {
        "Collection": {
            "type": "object",
            "title": "collection",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "#\/definitions\/Item"
                    }
                }
            }
        },
        "Item": {
            "type": "object",
            "title": "item",
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "pattern": "[A-z]+",
                    "minLength": 3,
                    "maxLength": 16
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            },
            "required": [
                "id"
            ]
        },
        "Message": {
            "type": "object",
            "title": "message",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }
}
