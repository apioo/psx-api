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
use PSX\Schema\Generator\Html;
use PSX\Schema\Parser\JsonSchema\RefResolver;
use Symfony\Component\Yaml\Yaml;

/**
 * ResourceTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceTest extends ApiManagerTestCase
{
    public function testHtml()
    {
        $resources = $this->getResources();
        $generator = new Generator\Html\Schema(new Html());

        $expect = $this->getExpectedHtml();
        $actual = $generator->generate($resources['/pets']);

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    public function testOpenAPI()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');
        $reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $resources = $this->getResources();
        $generator = new Generator\OpenAPI($reader, 1, '/', 'urn:schema.phpsx.org#');

        $expect = $this->getExpectedOpenAPI();
        $actual = $generator->generate($resources['/pets']);

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testPhp()
    {
        $resources = $this->getResources();
        $generator = new Generator\Php();

        $expect = $this->getExpectedPhp();
        $actual = $generator->generate($resources['/pets']);

        $this->assertEquals(str_replace(array("\r\n", "\r"), "\n", $expect), $actual, $actual);
    }

    public function testRaml()
    {
        $resources = $this->getResources();
        $generator = new Generator\Raml('PSX', 1, 'http://api.phpsx.org', 'urn:schema.phpsx.org#');

        $expect = $this->getExpectedRaml();
        $actual = $generator->generate($resources['/pets']);

        $expect = json_encode(Yaml::parse($expect));
        $actual = json_encode(Yaml::parse($actual));

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testSwagger()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');
        $reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $resources = $this->getResources();
        $generator = new Generator\Swagger($reader, 1, '/', 'urn:schema.phpsx.org#');

        $expect = $this->getExpectedSwagger();
        $actual = $generator->generate($resources['/pets']);

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    private function getExpectedHtml()
    {
        return <<<HTML
<div class="psx-resource psx-api-generator-html-schema" data-status="1" data-path="/pets">
	<h4>Schema</h4>
	<div class="psx-resource-description">foobar</div>
	<div class="psx-resource-method" data-method="GET">
		<div class="psx-resource-method-description">List all pets</div>
		<div class="psx-resource-data psx-resource-query">
			<h5>GET Query-Parameters</h5>
			<div class="psx-resource-data-content">
				<div id="psx_model_Query" class="psx-object">
					<h1>query</h1>
					<pre class="psx-object-json">
						<span class="psx-object-json-pun">{</span>
						<span class="psx-object-json-key">"limit"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">Integer</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-pun">}</span>
					</pre>
					<table class="table psx-object-properties">
						<colgroup>
							<col width="30%" />
							<col width="70%" />
						</colgroup>
						<thead>
							<tr>
								<th>Field</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">limit</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">Integer</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>GET Response - 200 OK</h5>
			<div class="psx-resource-data-content">
				<div id="psx_model_Pets" class="psx-object">
					<h1>Pets</h1>
					<pre class="psx-object-json">
						<span class="psx-object-json-pun">{</span>
						<span class="psx-object-json-key">"pets"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type psx-property-type-array">Array (<span class="psx-property-type psx-property-type-object">Object (<a href="#psx_model_Pet">Pet</a>)</span>)</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-pun">}</span>
					</pre>
					<table class="table psx-object-properties">
						<colgroup>
							<col width="30%" />
							<col width="70%" />
						</colgroup>
						<thead>
							<tr>
								<th>Field</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">pets</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type psx-property-type-array">Array (<span class="psx-property-type psx-property-type-object">Object (<a href="#psx_model_Pet">Pet</a>)</span>)</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="psx_model_Pet" class="psx-object">
					<h1>Pet</h1>
					<pre class="psx-object-json">
						<span class="psx-object-json-pun">{</span>
						<span class="psx-object-json-key">"id"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">Integer</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-key">"name"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">String</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-key">"tag"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">String</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-pun">}</span>
					</pre>
					<table class="table psx-object-properties">
						<colgroup>
							<col width="30%" />
							<col width="70%" />
						</colgroup>
						<thead>
							<tr>
								<th>Field</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">id</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">Integer</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">name</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">String</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">tag</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">String</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>GET Response - 500 Internal Server Error</h5>
			<div class="psx-resource-data-content">
				<div id="psx_model_Error" class="psx-object">
					<h1>Error</h1>
					<pre class="psx-object-json">
						<span class="psx-object-json-pun">{</span>
						<span class="psx-object-json-key">"code"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">Integer</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-key">"message"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">String</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-pun">}</span>
					</pre>
					<table class="table psx-object-properties">
						<colgroup>
							<col width="30%" />
							<col width="70%" />
						</colgroup>
						<thead>
							<tr>
								<th>Field</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">code</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">Integer</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">message</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">String</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="psx-resource-method" data-method="POST">
		<div class="psx-resource-method-description">Create a pet</div>
		<div class="psx-resource-data psx-resource-request">
			<h5>POST Request</h5>
			<div class="psx-resource-data-content">
				<div id="psx_model_Pet" class="psx-object">
					<h1>Pet</h1>
					<pre class="psx-object-json">
						<span class="psx-object-json-pun">{</span>
						<span class="psx-object-json-key">"id"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">Integer</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-key">"name"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">String</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-key">"tag"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">String</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-pun">}</span>
					</pre>
					<table class="table psx-object-properties">
						<colgroup>
							<col width="30%" />
							<col width="70%" />
						</colgroup>
						<thead>
							<tr>
								<th>Field</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">id</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">Integer</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">name</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">String</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-optional">tag</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">String</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="psx-resource-data psx-resource-response">
			<h5>POST Response - 500 Internal Server Error</h5>
			<div class="psx-resource-data-content">
				<div id="psx_model_Error" class="psx-object">
					<h1>Error</h1>
					<pre class="psx-object-json">
						<span class="psx-object-json-pun">{</span>
						<span class="psx-object-json-key">"code"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">Integer</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-key">"message"</span>
						<span class="psx-object-json-pun">: </span>
						<span class="psx-property-type">String</span>
						<span class="psx-object-json-pun">,</span>
						<span class="psx-object-json-pun">}</span>
					</pre>
					<table class="table psx-object-properties">
						<colgroup>
							<col width="30%" />
							<col width="70%" />
						</colgroup>
						<thead>
							<tr>
								<th>Field</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">code</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">Integer</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
							<tr>
								<td>
									<span class="psx-property-name psx-property-required">message</span>
								</td>
								<td>
									<span class="psx-property-type">
										<span class="psx-property-type">String</span>
									</span>
									<br />
									<div class="psx-property-description"/>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
HTML;
    }

    private function getExpectedOpenAPI()
    {
        return <<<'JSON'
{
    "openapi": "3.0.0",
    "info": {
        "title": "PSX",
        "version": "1"
    },
    "servers": [
        {
            "url": "\/"
        }
    ],
    "paths": {
        "\/pets": {
            "get": {
                "description": "List all pets",
                "operationId": "listPets",
                "parameters": [
                    {
                        "name": "limit",
                        "in": "query",
                        "required": false,
                        "schema": {
                            "type": "integer",
                            "format": "int32"
                        }
                    }
                ],
                "responses": {
                    "200": {
                        "description": "GET 200 response",
                        "content": {
                            "application\/json": {
                                "schema": {
                                    "$ref": "#\/components\/schemas\/Pets"
                                }
                            }
                        }
                    },
                    "500": {
                        "description": "GET 500 response",
                        "content": {
                            "application\/json": {
                                "schema": {
                                    "$ref": "#\/components\/schemas\/Error"
                                }
                            }
                        }
                    }
                }
            },
            "post": {
                "description": "Create a pet",
                "operationId": "createPets",
                "requestBody": {
                    "content": {
                        "application\/json": {
                            "schema": {
                                "$ref": "#\/components\/schemas\/Pet"
                            }
                        }
                    }
                },
                "responses": {
                    "500": {
                        "description": "POST 500 response",
                        "content": {
                            "application\/json": {
                                "schema": {
                                    "$ref": "#\/components\/schemas\/Error"
                                }
                            }
                        }
                    }
                }
            },
            "parameters": []
        }
    },
    "components": {
        "schemas": {
            "Pets": {
                "title": "Pets",
                "properties": {
                    "pets": {
                        "type": "array",
                        "items": {
                            "$ref": "#\/components\/schemas\/Pet"
                        }
                    }
                }
            },
            "Error": {
                "title": "Error",
                "properties": {
                    "code": {
                        "type": "integer",
                        "format": "int32"
                    },
                    "message": {
                        "type": "string"
                    }
                },
                "required": [
                    "code",
                    "message"
                ]
            },
            "Pet": {
                "title": "Pet",
                "properties": {
                    "id": {
                        "type": "integer",
                        "format": "int64"
                    },
                    "name": {
                        "type": "string"
                    },
                    "tag": {
                        "type": "string"
                    }
                },
                "required": [
                    "id",
                    "name"
                ]
            }
        }
    }
}
JSON;
    }

    private function getExpectedPhp()
    {
        return <<<'TEXT'
<?php

namespace PSX\Generation;

use PSX\Framework\Controller\SchemaApiAbstract;
/**
 * @Description("foobar")
 */
class Endpoint extends SchemaApiAbstract
{
    /**
     * @Description("List all pets")
     * @QueryParam(name="limit", type="integer", format="int32")
     * @Outgoing(code=200, schema="PSX\Generation\Pets")
     * @Outgoing(code=500, schema="PSX\Generation\Error")
     */
    public function doGet($record)
    {
    }
    /**
     * @Description("Create a pet")
     * @Incoming(schema="PSX\Generation\Pet")
     * @Outgoing(code=500, schema="PSX\Generation\Error")
     */
    public function doPost($record)
    {
    }
}
namespace PSX\Generation;

/**
 * @Title("Pet")
 * @Required({"id", "name"})
 */
class Pet
{
    /**
     * @Key("id")
     * @Type("integer")
     * @Format("int64")
     */
    protected $id;
    /**
     * @Key("name")
     * @Type("string")
     */
    protected $name;
    /**
     * @Key("tag")
     * @Type("string")
     */
    protected $tag;
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setTag($tag)
    {
        $this->tag = $tag;
    }
    public function getTag()
    {
        return $this->tag;
    }
}
/**
 * @Title("Pets")
 */
class Pets
{
    /**
     * @Key("pets")
     * @Type("array")
     * @Items(@Ref("PSX\Generation\Pet"))
     */
    protected $pets;
    public function setPets($pets)
    {
        $this->pets = $pets;
    }
    public function getPets()
    {
        return $this->pets;
    }
}
namespace PSX\Generation;

/**
 * @Title("Error")
 * @Required({"code", "message"})
 */
class Error
{
    /**
     * @Key("code")
     * @Type("integer")
     * @Format("int32")
     */
    protected $code;
    /**
     * @Key("message")
     * @Type("string")
     */
    protected $message;
    public function setCode($code)
    {
        $this->code = $code;
    }
    public function getCode()
    {
        return $this->code;
    }
    public function setMessage($message)
    {
        $this->message = $message;
    }
    public function getMessage()
    {
        return $this->message;
    }
}
TEXT;
    }

    private function getExpectedRaml()
    {
        return <<<'YAML'
#%RAML 1.0
---
baseUri: 'http://api.phpsx.org'
version: v1
title: PSX
/pets:
  description: foobar
  get:
    description: 'List all pets'
    queryParameters:
      limit:
        type: integer
        required: false
    responses:
      200:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "definitions": {
                      "Pet": {
                          "title": "Pet",
                          "properties": {
                              "id": {
                                  "type": "integer",
                                  "format": "int64"
                              },
                              "name": {
                                  "type": "string"
                              },
                              "tag": {
                                  "type": "string"
                              }
                          },
                          "required": [
                              "id",
                              "name"
                          ]
                      }
                  },
                  "title": "Pets",
                  "properties": {
                      "pets": {
                          "type": "array",
                          "items": {
                              "$ref": "#\/definitions\/Pet"
                          }
                      }
                  }
              }
      500:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "title": "Error",
                  "properties": {
                      "code": {
                          "type": "integer",
                          "format": "int32"
                      },
                      "message": {
                          "type": "string"
                      }
                  },
                  "required": [
                      "code",
                      "message"
                  ]
              }
  post:
    description: 'Create a pet'
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
              "title": "Pet",
              "properties": {
                  "id": {
                      "type": "integer",
                      "format": "int64"
                  },
                  "name": {
                      "type": "string"
                  },
                  "tag": {
                      "type": "string"
                  }
              },
              "required": [
                  "id",
                  "name"
              ]
          }
    responses:
      500:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
                  "title": "Error",
                  "properties": {
                      "code": {
                          "type": "integer",
                          "format": "int32"
                      },
                      "message": {
                          "type": "string"
                      }
                  },
                  "required": [
                      "code",
                      "message"
                  ]
              }

YAML;
    }
    
    private function getExpectedSwagger()
    {
        return <<<'JSON'
{
    "swagger": "2.0",
    "info": {
        "title": "PSX",
        "version": "1"
    },
    "basePath": "\/",
    "paths": {
        "\/pets": {
            "get": {
                "description": "List all pets",
                "operationId": "listPets",
                "parameters": [
                    {
                        "name": "limit",
                        "in": "query",
                        "required": false,
                        "type": "integer",
                        "format": "int32"
                    }
                ],
                "responses": {
                    "200": {
                        "description": "GET 200 response",
                        "schema": {
                            "$ref": "#\/definitions\/Pets"
                        }
                    },
                    "500": {
                        "description": "GET 500 response",
                        "schema": {
                            "$ref": "#\/definitions\/Error"
                        }
                    }
                }
            },
            "post": {
                "description": "Create a pet",
                "operationId": "createPets",
                "parameters": [
                    {
                        "description": "POST request",
                        "name": "body",
                        "in": "body",
                        "required": true,
                        "schema": {
                            "$ref": "#\/definitions\/Pet"
                        }
                    }
                ],
                "responses": {
                    "500": {
                        "description": "POST 500 response",
                        "schema": {
                            "$ref": "#\/definitions\/Error"
                        }
                    }
                }
            },
            "parameters": []
        }
    },
    "definitions": {
        "Pets": {
            "title": "Pets",
            "properties": {
                "pets": {
                    "type": "array",
                    "items": {
                        "$ref": "#\/definitions\/Pet"
                    }
                }
            }
        },
        "Error": {
            "title": "Error",
            "properties": {
                "code": {
                    "type": "integer",
                    "format": "int32"
                },
                "message": {
                    "type": "string"
                }
            },
            "required": [
                "code",
                "message"
            ]
        },
        "Pet": {
            "title": "Pet",
            "properties": {
                "id": {
                    "type": "integer",
                    "format": "int64"
                },
                "name": {
                    "type": "string"
                },
                "tag": {
                    "type": "string"
                }
            },
            "required": [
                "id",
                "name"
            ]
        }
    }
}
JSON;
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

