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

use PSX\Api\Generator\Raml;

/**
 * RamlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RamlTest extends GeneratorTestCase
{
    public function testGenerate()
    {
        $generator = new Raml('foobar', 1, 'http://api.phpsx.org', 'urn:schema.phpsx.org#');
        $raml      = $generator->generate($this->getResource());

        $expect = <<<'RAML'
#%RAML 1.0
---
baseUri: 'http://api.phpsx.org'
version: v1
title: foobar
/foo/{name}/{type}:
  description: 'lorem ipsum'
  uriParameters:
    name:
      type: string
      description: 'Name parameter'
      required: true
      minLength: 0
      maxLength: 16
      pattern: '[A-z]+'
    type:
      type: string
      required: false
      enum: [foo, bar]
  get:
    description: 'Returns a collection'
    queryParameters:
      startIndex:
        type: integer
        description: 'startIndex parameter'
        required: true
        minimum: 0
        maximum: 32
      float:
        type: number
        required: false
      boolean:
        type: boolean
        required: false
      date:
        type: date-only
        required: false
      datetime:
        type: datetime-only
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
                          }
                      }
                  },
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
              }
  post:
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
                  "title",
                  "date"
              ]
          }
    responses:
      201:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
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
  put:
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
          }
    responses:
      200:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
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
  delete:
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
          }
    responses:
      200:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
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
  patch:
    body:
      application/json:
        type: |
          {
              "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
              "id": "urn:schema.phpsx.org#",
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
          }
    responses:
      200:
        body:
          application/json:
            type: |
              {
                  "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
                  "id": "urn:schema.phpsx.org#",
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

RAML;

        $this->assertEquals(str_replace(array("\r\n", "\r"), "\n", $expect), $raml, $raml);
    }
}
