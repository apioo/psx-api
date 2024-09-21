<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Generator\Client\Util;

use PSX\Api\Exception\InvalidTypeException;
use PSX\Schema\ContentType;
use PSX\Schema\Generator\Normalizer\NormalizerInterface;
use PSX\Schema\Type\ArrayType;
use PSX\Schema\Type\MapType;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\Type\StringType;
use PSX\Schema\TypeInterface;

/**
 * Naming
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Naming
{
    private NormalizerInterface $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function buildClassNameByTag(array $parts): string
    {
        return $this->normalizer->class(...array_merge($parts, ['Tag']));
    }

    public function buildMethodNameByTag(string $tagName): string
    {
        return $this->normalizer->method($tagName);
    }

    public function buildExceptionClassNameByType(TypeInterface|ContentType $type): string
    {
        if ($type instanceof ContentType) {
            return match ($type->getShape()) {
                ContentType::BINARY => 'BinaryException',
                ContentType::FORM => 'FormException',
                ContentType::JSON => 'JsonException',
                ContentType::MULTIPART => 'MultipartException',
                ContentType::TEXT => 'TextException',
                ContentType::XML => 'XmlException',
            };
        } elseif ($type instanceof ReferenceType) {
            return $this->normalizer->class($type->getRef(), 'Exception');
        } elseif ($type instanceof MapType) {
            return 'Map' . $this->buildExceptionClassNameByType($type->getAdditionalProperties());
        } elseif ($type instanceof ArrayType) {
            return 'Array' . $this->buildExceptionClassNameByType($type->getItems());
        } else {
            throw new InvalidTypeException('Provided an invalid type must be reference, map or array type');
        }
    }

    public function buildMethodNameByOperationId(string $operationId): string
    {
        $parts = explode('.', $operationId);
        $methodName = end($parts);

        return $this->normalizer->method($methodName);
    }
}
