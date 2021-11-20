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

namespace PSX\Api\Generator\Client;

use PSX\Schema;
use PSX\Schema\GeneratorInterface;

/**
 * Java
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Java extends LanguageAbstract
{
    /**
     * @inheritdoc
     */
    protected function getTemplate(): string
    {
        return 'java.java.twig';
    }

    /**
     * @inheritdoc
     */
    protected function getClientTemplate(): string
    {
        return 'java-client.java.twig';
    }

    /**
     * @inheritdoc
     */
    protected function getGenerator(): GeneratorInterface
    {
        return new Schema\Generator\Java($this->namespace);
    }

    /**
     * @inheritDoc
     */
    protected function getFileName(string $identifier): string
    {
        return $identifier . '.java';
    }

    /**
     * @inheritDoc
     */
    protected function getFileContent(string $code, string $identifier): string
    {
        $comment = '/**' . "\n";
        $comment.= ' * ' . $identifier . ' generated on ' . date('Y-m-d') . "\n";
        $comment.= ' * @see https://sdkgen.app' . "\n";
        $comment.= ' */' . "\n";

        return $comment . "\n" . $code;
    }
}