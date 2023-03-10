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

namespace PSX\Api\Tests\Generator\Schema;

/**
 * TestRequest
 */
class TestRequest
{
    private ?int $int = null;
    private ?float $float = null;
    private ?string $string = null;
    private ?bool $bool = null;
    /**
     * @var array<string>
     */
    private ?array $arrayScalar = null;
    /**
     * @var array<\PSX\Api\Tests\Generator\Schema\TestObject>
     */
    private ?array $arrayObject = null;
    private ?TestMapScalar $mapScalar = null;
    private ?TestMapObject $mapObject = null;
    private ?TestObject $object = null;

    public function getInt(): ?int
    {
        return $this->int;
    }

    public function setInt(?int $int): void
    {
        $this->int = $int;
    }

    public function getFloat(): ?float
    {
        return $this->float;
    }

    public function setFloat(?float $float): void
    {
        $this->float = $float;
    }

    public function getString(): ?string
    {
        return $this->string;
    }

    public function setString(?string $string): void
    {
        $this->string = $string;
    }

    public function getBool(): ?bool
    {
        return $this->bool;
    }

    public function setBool(?bool $bool): void
    {
        $this->bool = $bool;
    }

    public function getArrayScalar(): ?array
    {
        return $this->arrayScalar;
    }

    public function setArrayScalar(?array $arrayScalar): void
    {
        $this->arrayScalar = $arrayScalar;
    }

    public function getArrayObject(): ?array
    {
        return $this->arrayObject;
    }

    public function setArrayObject(?array $arrayObject): void
    {
        $this->arrayObject = $arrayObject;
    }

    public function getMapScalar(): ?TestMapScalar
    {
        return $this->mapScalar;
    }

    public function setMapScalar(?TestMapScalar $mapScalar): void
    {
        $this->mapScalar = $mapScalar;
    }

    public function getMapObject(): ?TestMapObject
    {
        return $this->mapObject;
    }

    public function setMapObject(?TestMapObject $mapObject): void
    {
        $this->mapObject = $mapObject;
    }

    public function getObject(): ?TestObject
    {
        return $this->object;
    }

    public function setObject(?TestObject $object): void
    {
        $this->object = $object;
    }
}
