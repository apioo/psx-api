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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Api\Generator\Swagger;
use PSX\Data\Exporter\Popo;

/**
 * SwaggerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SwaggerTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $exporter  = new Popo($reader);
        $generator = new Swagger($exporter, 1, 'http://api.phpsx.org', 'http://foo.phpsx.org');
        $json      = $generator->generate($this->getResource());

        $expect = <<<'JSON'
{
    "swaggerVersion": "1.2",
    "apiVersion": 1,
    "basePath": "http:\/\/api.phpsx.org",
    "resourcePath": "\/foo\/bar",
    "apis": [
        {
            "path": "\/foo\/bar",
            "description": "lorem ipsum",
            "operations": [
                {
                    "method": "GET",
                    "summary": "Returns a collection",
                    "nickname": "getCollection",
                    "parameters": [
                        {
                            "paramType": "path",
                            "name": "name",
                            "description": "Name parameter",
                            "required": false,
                            "type": "string",
                            "minimum": 0,
                            "maximum": 16
                        },
                        {
                            "paramType": "path",
                            "name": "type",
                            "type": "string",
                            "enum": [
                                "foo",
                                "bar"
                            ]
                        },
                        {
                            "paramType": "query",
                            "name": "startIndex",
                            "description": "startIndex parameter",
                            "required": false,
                            "type": "integer",
                            "minimum": 0,
                            "maximum": 32
                        },
                        {
                            "paramType": "query",
                            "name": "float",
                            "type": "number"
                        },
                        {
                            "paramType": "query",
                            "name": "boolean",
                            "type": "boolean"
                        },
                        {
                            "paramType": "query",
                            "name": "date",
                            "type": "string",
                            "format": "date"
                        },
                        {
                            "paramType": "query",
                            "name": "datetime",
                            "type": "string",
                            "format": "date-time"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "200 response",
                            "responseModel": "GET-200-response"
                        }
                    ]
                },
                {
                    "method": "POST",
                    "nickname": "postItem",
                    "parameters": [
                        {
                            "paramType": "path",
                            "name": "name",
                            "description": "Name parameter",
                            "required": false,
                            "type": "string",
                            "minimum": 0,
                            "maximum": 16
                        },
                        {
                            "paramType": "path",
                            "name": "type",
                            "type": "string",
                            "enum": [
                                "foo",
                                "bar"
                            ]
                        },
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "POST-request"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 201,
                            "message": "201 response",
                            "responseModel": "POST-201-response"
                        }
                    ]
                },
                {
                    "method": "PUT",
                    "nickname": "putItem",
                    "parameters": [
                        {
                            "paramType": "path",
                            "name": "name",
                            "description": "Name parameter",
                            "required": false,
                            "type": "string",
                            "minimum": 0,
                            "maximum": 16
                        },
                        {
                            "paramType": "path",
                            "name": "type",
                            "type": "string",
                            "enum": [
                                "foo",
                                "bar"
                            ]
                        },
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "PUT-request"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "200 response",
                            "responseModel": "PUT-200-response"
                        }
                    ]
                },
                {
                    "method": "DELETE",
                    "nickname": "deleteItem",
                    "parameters": [
                        {
                            "paramType": "path",
                            "name": "name",
                            "description": "Name parameter",
                            "required": false,
                            "type": "string",
                            "minimum": 0,
                            "maximum": 16
                        },
                        {
                            "paramType": "path",
                            "name": "type",
                            "type": "string",
                            "enum": [
                                "foo",
                                "bar"
                            ]
                        },
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "DELETE-request"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "200 response",
                            "responseModel": "DELETE-200-response"
                        }
                    ]
                },
                {
                    "method": "PATCH",
                    "nickname": "patchItem",
                    "parameters": [
                        {
                            "paramType": "path",
                            "name": "name",
                            "description": "Name parameter",
                            "required": false,
                            "type": "string",
                            "minimum": 0,
                            "maximum": 16
                        },
                        {
                            "paramType": "path",
                            "name": "type",
                            "type": "string",
                            "enum": [
                                "foo",
                                "bar"
                            ]
                        },
                        {
                            "paramType": "body",
                            "name": "body",
                            "required": true,
                            "type": "PATCH-request"
                        }
                    ],
                    "responseMessages": [
                        {
                            "code": 200,
                            "message": "200 response",
                            "responseModel": "PATCH-200-response"
                        }
                    ]
                }
            ]
        }
    ],
    "models": {
        "ref1a543de6ef793b231e7e4c78844dbc84": {
            "id": "ref1a543de6ef793b231e7e4c78844dbc84",
            "properties": {
                "name": {
                    "description": "Name parameter",
                    "type": "string",
                    "maximum": 16
                },
                "type": {
                    "type": "string",
                    "enum": [
                        "foo",
                        "bar"
                    ]
                }
            }
        },
        "ref21726c1551deab178a68a7ffac656c75": {
            "id": "ref21726c1551deab178a68a7ffac656c75",
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
            }
        },
        "ref7bde1c36c5f13fd4cf10c2864f8e8a75": {
            "id": "ref7bde1c36c5f13fd4cf10c2864f8e8a75",
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minimum": 3,
                    "maximum": 16
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            }
        },
        "refc6491059d9103dc5bb112e51828416d9": {
            "id": "refc6491059d9103dc5bb112e51828416d9",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "ref7bde1c36c5f13fd4cf10c2864f8e8a75"
                    }
                }
            }
        },
        "ref70152cdfc48a8a3969f10e9e4fe3b239": {
            "id": "ref70152cdfc48a8a3969f10e9e4fe3b239",
            "required": [
                "title",
                "date"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minimum": 3,
                    "maximum": 16
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            }
        },
        "ref31ead4d236fd038a7d55a40e2ca1171e": {
            "id": "ref31ead4d236fd038a7d55a40e2ca1171e",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "ref774a7a4ece700fad7bb605e81c61fea7": {
            "id": "ref774a7a4ece700fad7bb605e81c61fea7",
            "required": [
                "id"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minimum": 3,
                    "maximum": 16
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            }
        },
        "GET-query": {
            "id": "GET-query",
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
            }
        },
        "GET-200-response": {
            "id": "GET-200-response",
            "properties": {
                "entry": {
                    "type": "array",
                    "items": {
                        "$ref": "ref7bde1c36c5f13fd4cf10c2864f8e8a75"
                    }
                }
            }
        },
        "POST-request": {
            "id": "POST-request",
            "required": [
                "title",
                "date"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minimum": 3,
                    "maximum": 16
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            }
        },
        "POST-201-response": {
            "id": "POST-201-response",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "PUT-request": {
            "id": "PUT-request",
            "required": [
                "id"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minimum": 3,
                    "maximum": 16
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            }
        },
        "PUT-200-response": {
            "id": "PUT-200-response",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "DELETE-request": {
            "id": "DELETE-request",
            "required": [
                "id"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minimum": 3,
                    "maximum": 16
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            }
        },
        "DELETE-200-response": {
            "id": "DELETE-200-response",
            "properties": {
                "success": {
                    "type": "boolean"
                },
                "message": {
                    "type": "string"
                }
            }
        },
        "PATCH-request": {
            "id": "PATCH-request",
            "required": [
                "id"
            ],
            "properties": {
                "id": {
                    "type": "integer"
                },
                "userId": {
                    "type": "integer"
                },
                "title": {
                    "type": "string",
                    "minimum": 3,
                    "maximum": 16
                },
                "date": {
                    "type": "string",
                    "format": "date-time"
                }
            }
        },
        "PATCH-200-response": {
            "id": "PATCH-200-response",
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
