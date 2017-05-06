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

namespace PSX\Api\Tests\Parser;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Api\Parser\Annotation as AnnotationParser;
use PSX\Api\Tests\Parser\Annotation\TestController;
use PSX\Schema\SchemaManager;


/**
 * AnnotationTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AnnotationTest extends ParserTestCase
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $annotationReader;

    /**
     * @var \PSX\Schema\SchemaManager
     */
    protected $schemaManager;

    protected function setUp()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');

        $this->annotationReader = $reader;
        $this->schemaManager    = new SchemaManager($reader);
    }

    protected function getResource()
    {
        $annotation = new AnnotationParser(
            $this->annotationReader,
            $this->schemaManager
        );

        return $annotation->parse(TestController::class, '/foo');
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testParseInvalid()
    {
        $annotation = new AnnotationParser(
            $this->annotationReader,
            $this->schemaManager
        );

        $annotation->parse('foo', '/foo');
    }
}

