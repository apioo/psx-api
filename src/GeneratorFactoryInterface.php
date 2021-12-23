<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

/**
 * GeneratorFactoryInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface GeneratorFactoryInterface
{
    const CLIENT_GO = 'client-go';
    const CLIENT_JAVA = 'client-java';
    const CLIENT_PHP = 'client-php';
    const CLIENT_TYPESCRIPT = 'client-typescript';

    const MARKUP_HTML = 'markup-html';
    const MARKUP_MARKDOWN = 'markup-markdown';

    const SPEC_TYPESCHEMA = 'spec-typeschema';
    const SPEC_OPENAPI = 'spec-openapi';
    const SPEC_RAML = 'spec-raml';

    /**
     * Returns the fitting generator object for the provided type
     * 
     * @param string $format
     * @param string|null $config
     * @return GeneratorInterface
     */
    public function getGenerator(string $format, ?string $config = null): GeneratorInterface;

    /**
     * Returns the preferred file extension for the provided format
     * 
     * @param string $format
     * @param string|null $config
     * @return string
     */
    public function getFileExtension(string $format, ?string $config = null): string;

    /**
     * Returns the preferred mime for the provided format
     * 
     * @param string $format
     * @param string|null $config
     * @return string
     */
    public function getMime(string $format, ?string $config = null): string;
}
