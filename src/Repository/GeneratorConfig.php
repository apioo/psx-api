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

namespace PSX\Api\Repository;

use PSX\Api\GeneratorInterface;
use PSX\Schema\Generator\Config;

/**
 * The GeneratorConfig contains all information about a specific generator and is returned by a repository
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class GeneratorConfig
{
    private \Closure $factory;
    private string $fileExtension;
    private string $mime;

    public function __construct(\Closure $factory, string $fileExtension, string $mime)
    {
        $this->factory = $factory;
        $this->fileExtension = $fileExtension;
        $this->mime = $mime;
    }

    public function newInstance(?string $baseUrl, ?Config $config): GeneratorInterface
    {
        return call_user_func_array($this->factory, [$baseUrl, $config]);
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    public function getMime(): string
    {
        return $this->mime;
    }
}
