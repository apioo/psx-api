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

namespace PSX\Api;

use PSX\Api\Builder\SpecificationBuilderInterface;
use PSX\Api\Exception\InvalidApiException;
use PSX\Schema\Parser\ContextInterface;

/**
 * ApiManagerInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface ApiManagerInterface
{
    /**
     * Registers a new parser for the provided scheme
     */
    public function register(string $scheme, ParserInterface $parser): void;

    /**
     * The api manager knows how to create a specification from the given schema name. Returns a specification interface
     * or throws an exception.
     *
     * The source can be an uri format where you can specify a fitting parser i.e.
     *  - php://My.Acme.Dto
     *    Resolves the specification from a class containing attributes describing the specification
     *  - file:///path/to/a/file.json
     *    Resolves the specification by parsing an TypeAPI or OpenAPI file
     *
     * If the source is a simple string the manager tries to guess the fitting source uri format
     *
     * @throws InvalidApiException
     */
    public function getApi(string $source, ?ContextInterface $context = null): SpecificationInterface;

    /**
     * Clears the cache for a specific source
     */
    public function clear(string $source): void;

    /**
     * Returns a builder which helps to create a specification
     */
    public function getBuilder(): SpecificationBuilderInterface;
}
