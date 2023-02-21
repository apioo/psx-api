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

namespace PSX\Api\Generator\Client;

use PSX\Schema;
use PSX\Schema\GeneratorInterface;

/**
 * Typescript
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Typescript extends LanguageAbstract
{
    protected function getOperationTemplate(): string
    {
        return 'typescript-operation.ts.twig';
    }

    protected function getTagTemplate(): string
    {
        return 'typescript-tag.ts.twig';
    }

    protected function getExceptionTemplate(): string
    {
        return 'typescript-exception.ts.twig';
    }

    protected function getClientTemplate(): string
    {
        return 'typescript-client.ts.twig';
    }

    protected function newGenerator(): GeneratorInterface
    {
        return new Schema\Generator\TypeScript($this->namespace);
    }

    protected function getFileExtension(): string
    {
        return 'ts';
    }

    protected function getFileContent(string $code, string $identifier): string
    {
        $comment = '/**' . "\n";
        $comment.= ' * ' . $identifier . ' automatically generated by SDKgen please do not edit this file manually' . "\n";
        $comment.= ' * {@link https://sdkgen.app}' . "\n";
        $comment.= ' */' . "\n";

        return $comment . "\n" . $code;
    }
}
