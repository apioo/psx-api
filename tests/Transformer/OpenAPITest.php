<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Tests\Transformer;

use PHPUnit\Framework\TestCase;
use PSX\Api\ApiManager;
use PSX\Api\Parser\Attribute\OperationIdBuilder;
use PSX\Api\SpecificationInterface;
use PSX\Api\Transformer\OpenAPI;
use PSX\Schema\SchemaManager;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Yaml\Yaml;

/**
 * OpenAPITest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OpenAPITest extends TestCase
{
    /**
     * @dataProvider transformProvider
     */
    public function testConvert(string $file)
    {
        $schema = file_get_contents(__DIR__ . '/openapi/actual/' . $file);
        if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['yml', 'yaml'])) {
            $file = pathinfo($file, PATHINFO_FILENAME) . '.json';
            $data = \json_decode(\json_encode(Yaml::parse($schema)));
        } else {
            $data = \json_decode($schema);
        }

        $actual = (new OpenAPI())->transform($data);

        $expectFile = __DIR__ . '/openapi/expect/' . $file;
        $this->assertJsonStringEqualsJsonFile($expectFile, \json_encode($actual));

        // test whether we can parse the spec file
        $spec = (new ApiManager(new SchemaManager(), new OperationIdBuilder(new ArrayAdapter(), false)))->getApi($expectFile);
        $this->assertInstanceOf(SpecificationInterface::class, $spec);
    }

    public function transformProvider(): array
    {
        $result = [];
        $tests = scandir(__DIR__ . '/openapi/actual');
        foreach ($tests as $file) {
            if ($file[0] === '.') {
                continue;
            }

            $result[] = [$file];
        }

        return $result;
    }
}
