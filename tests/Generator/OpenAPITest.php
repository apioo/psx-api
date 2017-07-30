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
use PSX\Api\Generator\OpenAPI;
use PSX\Api\Generator\Swagger;

/**
 * OpenAPITest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OpenAPITest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $generator = new OpenAPI($reader, 1, 'http://api.phpsx.org', 'http://foo.phpsx.org');
        $json      = $generator->generate($this->getResource());

        $expect = <<<'JSON'
{
    "openapi": "3.0.0",
    "info": {
        "title": "PSX",
        "version": "1"
    },
    "servers": [
        {
            "url": "http:\/\/api.phpsx.org"
        }
    ],
    "paths": {
        "\/foo\/{name}\/{type}": {
            "get": {
                "description": "Returns a collection",
                "operationId": "getCollection",
                "parameters": [
                    {
                        "name": "startIndex",
                        "in": "query",
                        "description": "startIndex parameter",
                        "required": true,
                        "schema": {
                            "type": "integer",
                            "description": "startIndex parameter",
                            "minimum": 0,
                            "maximum": 32
                        }
                    },
                    {
                        "name": "float",
                        "in": "query",
                        "required": false,
                        "schema": {
                            "type": "number"
                        }
                    },
                    {
                        "name": "boolean",
                        "in": "query",
                        "required": false,
                        "schema": {
                            "type": "boolean"
                        }
                    },
                    {
                        "name": "date",
                        "in": "query",
                        "required": false,
                        "schema": {
                            "type": "string",
                            "format": "date"
                        }
                    },
                    {
                        "name": "datetime",
                        "in": "query",
                        "required": false,
                        "schema": {
                            "type": "string",
                            "format": "date-time"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "GET 200 response",
                        "content": {
                            "application\/json": {
                                "schema": {
                                    "$ref": "#\/components\/schemas\/Collection"
                                }
                            }
                        }
                    }
                }
            },
            "put": {
                "operationId": "putItem",
                "requestBody": {
                    "content": {
                        "application\/json": {
                            "schema": {
                                "$ref": "#\/components\/schemas\/Item"
                            }
                        }
                    }
                },
                "responses": {
                    "200": {
                        "description": "PUT 200 response",
                        "content": {
                            "application\/json": {
                                "schema": {
                                    "$ref": "#\/components\/schemas\/Message"
                                }
                            }
                        }
                    }
                }
            },
            "post": {
                "operationId": "postItem",
                "requestBody": {
                    "content": {
                        "application\/json": {
                            "schema": {
                                "$ref": "#\/components\/schemas\/Item"
                            }
                        }
                    }
                },
                "responses": {
                    "201": {
                        "description": "POST 201 response",
                        "content": {
                            "application\/json": {
                                "schema": {
                                    "$ref": "#\/components\/schemas\/Message"
                                }
                            }
                        }
                    }
                }
            },
            "delete": {
                "operationId": "deleteItem",
                "requestBody": {
                    "content": {
                        "application\/json": {
                            "schema": {
                                "$ref": "#\/components\/schemas\/Item"
                            }
                        }
                    }
                },
                "responses": {
                    "200": {
                        "description": "DELETE 200 response",
                        "content": {
                            "application\/json": {
                                "schema": {
                                    "$ref": "#\/components\/schemas\/Message"
                                }
                            }
                        }
                    }
                }
            },
            "patch": {
                "operationId": "patchItem",
                "requestBody": {
                    "content": {
                        "application\/json": {
                            "schema": {
                                "$ref": "#\/components\/schemas\/Item"
                            }
                        }
                    }
                },
                "responses": {
                    "200": {
                        "description": "PATCH 200 response",
                        "content": {
                            "application\/json": {
                                "schema": {
                                    "$ref": "#\/components\/schemas\/Message"
                                }
                            }
                        }
                    }
                }
            },
            "parameters": [
                {
                    "name": "name",
                    "in": "path",
                    "description": "Name parameter",
                    "required": true,
                    "schema": {
                        "type": "string",
                        "description": "Name parameter",
                        "pattern": "[A-z]+",
                        "minLength": 0,
                        "maxLength": 16
                    }
                },
                {
                    "name": "type",
                    "in": "path",
                    "required": true,
                    "schema": {
                        "type": "string",
                        "enum": [
                            "foo",
                            "bar"
                        ]
                    }
                }
            ]
        }
    },
    "components": {
        "schemas": {
            "Collection": {
                "type": "object",
                "title": "collection",
                "properties": {
                    "entry": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/components\/schemas\/Item"
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
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }
}
