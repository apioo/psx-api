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

namespace PSX\Api\Generator;

use PSX\Api\SecurityInterface;
use PSX\Api\SpecificationInterface;

/**
 * ConfigurationTrait
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
trait ConfigurationTrait
{
    protected ?string $baseUrl;
    protected ?SecurityInterface $security = null;

    public function setBaseUrl(?string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function setSecurity(?SecurityInterface $security): void
    {
        $this->security = $security;
    }

    protected function getBaseUrl(SpecificationInterface $specification): ?string
    {
        $baseUrl = $specification->getBaseUrl();
        if (!empty($baseUrl)) {
            return $baseUrl;
        } elseif (!empty($this->baseUrl)) {
            return $this->baseUrl;
        } else {
            return null;
        }
    }

    protected function getSecurity(SpecificationInterface $specification): ?SecurityInterface
    {
        $security = $specification->getSecurity();
        if ($security instanceof SecurityInterface) {
            return $security;
        } elseif ($this->security instanceof SecurityInterface) {
            return $this->security;
        } else {
            return null;
        }
    }
}