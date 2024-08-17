<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2024 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Generator\Server\Dto;

/**
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Folder
{
    private array $folders;
    private array $files;

    public function __construct()
    {
        $this->folders = [];
        $this->files = [];
    }

    /**
     * @return array<string, Folder>
     */
    public function getFolders(): array
    {
        return $this->folders;
    }

    public function hasFolders(): bool
    {
        return count($this->folders) > 0;
    }

    public function addFolder(string $name, Folder $folder): void
    {
        $this->folders[$name] = $folder;
    }

    public function getFolder(string $name): ?Folder
    {
        return $this->folders[$name] ?? null;
    }

    /**
     * @return array<string, File>
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    public function addFile(string $name, File $file): void
    {
        $this->files[$name] = $file;
    }

    public function getFile(string $name): ?File
    {
        return $this->files[$name] ?? null;
    }

}
