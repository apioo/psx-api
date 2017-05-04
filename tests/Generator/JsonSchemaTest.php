<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Api\Generator\JsonSchema;

/**
 * JsonSchemaTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonSchemaTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new JsonSchema('urn:foo:bar', 'http://api.phpsx.org', 'http://foo.phpsx.org');
        $json      = $generator->generate($this->getResource());

        $expect = <<<'JSON'
{
    "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
    "id": "urn:foo:bar",
    "definitions": {
        "path-template": {
            "type": "object",
            "title": "path",
            "properties": {
                "name": {
                    "type": "string",
                    "description": "Name parameter",
                    "pattern": "[A-z]+",
                    "minLength": 0,
                    "maxLength": 16
                },
                "type": {
                    "type": "string",
                    "enum": [
                        "foo",
                        "bar"
                    ]
                }
            },
            "required": [
                "name"
            ]
        },
        "GET-query": {
            "type": "object",
            "title": "query",
            "properties": {
                "startIndex": {
                    "type": "integer",
                    "description": "startIndex parameter",
                    "minimum": 0,
                    "maximum": 32
                },
                "float": {
                    "type": "number"
                },
                "boolean": {
                    "type": "boolean"
                },
                "date": {
                    "type": "string",
                    "format": "date"
                },
                "datetime": {
                    "type": "string",
                    "format": "date-time"
                }
            },
            "required": [
                "startIndex"
            ]
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
        },
        "GET-200-response": {
            "$ref": "#\/definitions\/Collection"
        },
        "POST-request": {
            "$ref": "#\/definitions\/Item"
        },
        "POST-201-response": {
            "$ref": "#\/definitions\/Message"
        },
        "PUT-request": {
            "$ref": "#\/definitions\/Item"
        },
        "PUT-200-response": {
            "$ref": "#\/definitions\/Message"
        },
        "DELETE-request": {
            "$ref": "#\/definitions\/Item"
        },
        "DELETE-200-response": {
            "$ref": "#\/definitions\/Message"
        },
        "PATCH-request": {
            "$ref": "#\/definitions\/Item"
        },
        "PATCH-200-response": {
            "$ref": "#\/definitions\/Message"
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }
}
