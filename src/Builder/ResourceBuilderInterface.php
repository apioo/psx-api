<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Builder;

use PSX\Api\Exception\InvalidMethodException;
use PSX\Api\Resource;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Builder;
use PSX\Schema\DefinitionsInterface;

/**
 * ResourceBuilderInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface ResourceBuilderInterface
{
    /**
     * @param string $title
     */
    public function setTitle(string $title): void;

    /**
     * @param string $description
     */
    public function setDescription(string $description): void;

    /**
     * @param string $typeName
     * @return Builder
     */
    public function setPathParameters(string $typeName): Builder;

    /**
     * @param string $methodName
     * @return MethodBuilderInterface
     * @throws InvalidMethodException
     */
    public function addMethod(string $methodName): MethodBuilderInterface;

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void;

    /**
     * @return Resource
     */
    public function getResource(): Resource;

    /**
     * @return DefinitionsInterface
     */
    public function getDefinitions(): DefinitionsInterface;
}
