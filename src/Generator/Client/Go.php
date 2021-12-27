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

namespace PSX\Api\Generator\Client;

use PSX\Schema;
use PSX\Schema\GeneratorInterface;

/**
 * Go
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Go extends LanguageAbstract
{
    protected function getTemplate(): string
    {
        return 'go.go.twig';
    }

    protected function getGroupTemplate(): string
    {
        return 'go-group.go.twig';
    }

    protected function getClientTemplate(): string
    {
        return 'go-client.go.twig';
    }

    protected function getGenerator(): GeneratorInterface
    {
        return new Schema\Generator\Go($this->namespace);
    }

    protected function getFileName(string $identifier): string
    {
        return $this->underscore($identifier) . '.go';
    }

    protected function getFileContent(string $code, string $identifier): string
    {
        $comment = "\n";
        $comment.= '// ' . $identifier . ' generated on ' . date('Y-m-d') . "\n";
        $comment.= '// @see https://sdkgen.app' . "\n";
        $comment.= "\n";

        return $comment . "\n" . $code;
    }

    private function underscore(string $file): string
    {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), strtr($file, '_', '.')));
    }
}
