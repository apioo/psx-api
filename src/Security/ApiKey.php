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

namespace PSX\Api\Security;

use PSX\Api\SecurityInterface;

/**
 * ApiKey
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ApiKey implements SecurityInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $in;

    public function __construct(string $name, string $in)
    {
        $this->name = $name;
        $this->in = $in;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIn(): string
    {
        return $this->in;
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => 'apiKey',
            'name' => $this->name,
            'in' => $this->in,
        ], function($value){
            return $value !== null;
        });
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
