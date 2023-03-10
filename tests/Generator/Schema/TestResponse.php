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
 * TestResponse
 */
class TestResponse
{
    private ?TestMapScalar $args = null;
    private ?TestMapScalar $headers = null;
    private ?TestRequest $json = null;
    private ?string $method = null;

    public function getArgs(): ?TestMapScalar
    {
        return $this->args;
    }

    public function setArgs(?TestMapScalar $args): void
    {
        $this->args = $args;
    }

    public function getHeaders(): ?TestMapScalar
    {
        return $this->headers;
    }

    public function setHeaders(?TestMapScalar $headers): void
    {
        $this->headers = $headers;
    }

    public function getJson(): ?TestRequest
    {
        return $this->json;
    }

    public function setJson(?TestRequest $json): void
    {
        $this->json = $json;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): void
    {
        $this->method = $method;
    }
}
