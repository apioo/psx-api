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

namespace PSX\Api\Security;

use PSX\Api\SecurityInterface;

/**
 * OAuth2
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class OAuth2Abstract implements SecurityInterface
{
    protected string $tokenUrl;
    protected ?string $authorizationUrl;
    protected ?string $refreshUrl;
    protected ?array $scopes;

    public function __construct(string $tokenUrl, ?string $authorizationUrl, ?string $refreshUrl, ?array $scopes = null)
    {
        $this->tokenUrl = $tokenUrl;
        $this->authorizationUrl = $authorizationUrl;
        $this->refreshUrl = $refreshUrl;
        $this->scopes = $scopes;
    }

    public function getTokenUrl(): string
    {
        return $this->tokenUrl;
    }

    public function getAuthorizationUrl(): ?string
    {
        return $this->authorizationUrl;
    }

    public function getRefreshUrl(): ?string
    {
        return $this->refreshUrl;
    }

    public function getScopes(): ?array
    {
        return $this->scopes;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
