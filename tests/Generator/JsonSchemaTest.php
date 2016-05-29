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
        $generator = new JsonSchema('foo', 'http://api.phpsx.org', 'http://foo.phpsx.org');
        $json      = $generator->generate($this->getResource());

        $expect = <<<'JSON'
{
    "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
    "id": "foo",
    "type": "object",
    "definitions": {
        "ref1a543de6ef793b231e7e4c78844dbc84": {
            "title": "path",
            "type": "object",
            "properties": {
                "name": {
                    "description": "Name parameter",
                    "type": "string",
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "type": {
                    "type": "string",
                    "enum": [
                        "foo",
                        "bar"
                    ]
                }
            },
            "additionalProperties": true
        },
        "ref21726c1551deab178a68a7ffac656c75": {
            "title": "query",
            "type": "object",
            "properties": {
                "startIndex": {
                    "description": "startIndex parameter",
                    "type": "integer",
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
            "additionalProperties": true
        },
        "ref7bde1c36c5f13fd4cf10c2864f8e8a75": {
            "title": "item",
            "type": "object",
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            },
            "additionalProperties": false
        },
        "refc6491059d9103dc5bb112e51828416d9": {
            "title": "collection",
            "type": "object",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "#\/definitions\/ref7bde1c36c5f13fd4cf10c2864f8e8a75"
                    }
                }
            },
            "additionalProperties": false
        },
        "ref70152cdfc48a8a3969f10e9e4fe3b239": {
            "title": "item",
            "type": "object",
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            },
            "additionalProperties": false,
            "required": [
                "title",
                "date"
            ]
        },
        "ref31ead4d236fd038a7d55a40e2ca1171e": {
            "title": "message",
            "type": "object",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            },
            "additionalProperties": false
        },
        "ref774a7a4ece700fad7bb605e81c61fea7": {
            "title": "item",
            "type": "object",
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 16,
                    "pattern": "[A-z]+"
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            },
            "additionalProperties": false,
            "required": [
                "id"
            ]
        },
        "path": {
            "$ref": "#\/definitions\/ref1a543de6ef793b231e7e4c78844dbc84"
        },
        "GET-query": {
            "$ref": "#\/definitions\/ref21726c1551deab178a68a7ffac656c75"
        },
        "GET-200-response": {
            "$ref": "#\/definitions\/refc6491059d9103dc5bb112e51828416d9"
        },
        "POST-request": {
            "$ref": "#\/definitions\/ref70152cdfc48a8a3969f10e9e4fe3b239"
        },
        "POST-201-response": {
            "$ref": "#\/definitions\/ref31ead4d236fd038a7d55a40e2ca1171e"
        },
        "PUT-request": {
            "$ref": "#\/definitions\/ref774a7a4ece700fad7bb605e81c61fea7"
        },
        "PUT-200-response": {
            "$ref": "#\/definitions\/ref31ead4d236fd038a7d55a40e2ca1171e"
        },
        "DELETE-request": {
            "$ref": "#\/definitions\/ref774a7a4ece700fad7bb605e81c61fea7"
        },
        "DELETE-200-response": {
            "$ref": "#\/definitions\/ref31ead4d236fd038a7d55a40e2ca1171e"
        },
        "PATCH-request": {
            "$ref": "#\/definitions\/ref774a7a4ece700fad7bb605e81c61fea7"
        },
        "PATCH-200-response": {
            "$ref": "#\/definitions\/ref31ead4d236fd038a7d55a40e2ca1171e"
        }
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }
}
