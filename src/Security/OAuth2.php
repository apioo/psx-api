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

namespace PSX\Api\Security;

use PSX\Api\SecurityInterface;

/**
 * OAuth2
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OAuth2 implements SecurityInterface
{
    protected string $tokenUrl;
    protected ?string $authorizationUrl;
    protected ?array $scopes;

    public function __construct(string $tokenUrl, ?string $authorizationUrl, ?array $scopes = null)
    {
        $this->tokenUrl = $tokenUrl;
        $this->authorizationUrl = $authorizationUrl;
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

    public function getScopes(): ?array
    {
        return $this->scopes;
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => self::TYPE_OAUTH2,
            'tokenUrl' => $this->tokenUrl,
            'authorizationUrl' => $this->authorizationUrl,
            'scopes' => $this->scopes,
        ], function($value){
            return $value !== null;
        });
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
