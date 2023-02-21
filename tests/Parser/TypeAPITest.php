<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Api\ApiManager;
use PSX\Api\Parser\OpenAPI;
use PSX\Api\Resource;
use PSX\Api\ResourceCollection;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Type\StructType;
use PSX\Schema\TypeInterface;

/**
 * TypeAPITest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TypeAPITest extends ParserTestCase
{
    /**
     * @inheritDoc
     */
    protected function getSpecification(): SpecificationInterface
    {
        return $this->apiManager->getApi(__DIR__ . '/typeapi/simple.json', ApiManager::TYPE_TYPEAPI);
    }
}
