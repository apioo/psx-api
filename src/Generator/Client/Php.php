<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Schema\PropertyInterface;
use PSX\Schema\PropertyType;

/**
 * Php
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Php extends LanguageAbstract
{
    /**
     * @inheritdoc
     */
    protected function getDocType(PropertyInterface $property): string
    {
        $type = $this->getRealType($property);

        if ($type == PropertyType::TYPE_STRING) {
            return 'string';
        } elseif ($type == PropertyType::TYPE_NUMBER) {
            return 'float';
        } elseif ($type == PropertyType::TYPE_INTEGER) {
            return 'int';
        } elseif ($type == PropertyType::TYPE_BOOLEAN) {
            return 'bool';
        } elseif ($type == PropertyType::TYPE_ARRAY) {
            return $this->getIdentifierForProperty($property) . '[]';
        } elseif ($type == PropertyType::TYPE_OBJECT) {
            return $this->getIdentifierForProperty($property);
        } elseif ($property->getOneOf()) {
            $parts = [];
            foreach ($property->getOneOf() as $property) {
                $parts[] = $this->getType($property);
            }
            return implode('|', $parts);
        } elseif ($property->getAllOf()) {
            $parts = [];
            foreach ($property->getAllOf() as $property) {
                $parts[] = $this->getType($property);
            }
            return implode('&', $parts);
        } else {
            return 'mixed';
        }
    }

    /**
     * @inheritdoc
     */
    protected function getTemplate(): string
    {
        return 'php.ts.twig';
    }

    /**
     * @return string
     */
    protected function generateSchema(array $schemas)
    {
        $return = parent::generateSchema($schemas);

        $return = str_replace('<?php', '', $return);
        $return = str_replace('namespace PSX\Generation;', '', $return);

        return $return;
    }

    /**
     * @inheritdoc
     */
    protected function getGenerator(): GeneratorInterface
    {
        return new Schema\Generator\Php();
    }
}
