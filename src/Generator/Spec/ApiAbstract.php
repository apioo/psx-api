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

namespace PSX\Api\Generator\Spec;

use PSX\Api\GeneratorInterface;

/**
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class ApiAbstract implements GeneratorInterface
{
    public const FLOW_AUTHORIZATION_CODE = 0;
    public const FLOW_IMPLICIT = 1;
    public const FLOW_PASSWORD = 2;
    public const FLOW_CLIENT_CREDENTIALS = 3;

    protected ?string $title = null;
    protected ?string $description = null;
    protected ?string $tos = null;
    protected ?string $contactName = null;
    protected ?string $contactUrl = null;
    protected ?string $contactEmail = null;
    protected ?string $licenseName = null;
    protected ?string $licenseUrl = null;
    protected array $authFlows = [];
    protected array $tags = [];

    /**
     * The title of the application
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * A short description of the application. CommonMark syntax MAY be used for
     * rich text representation
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * A URL to the Terms of Service for the API. MUST be in the format of a URL
     */
    public function setTermsOfService(?string $tos): void
    {
        $this->tos = $tos;
    }

    /**
     * The identifying name of the contact person/organization
     */
    public function setContactName(?string $contactName): void
    {
        $this->contactName = $contactName;
    }

    /**
     * The URL pointing to the contact information. MUST be in the format of a URL
     */
    public function setContactUrl(?string $contactUrl): void
    {
        $this->contactUrl = $contactUrl;
    }

    /**
     * The email address of the contact person/organization. MUST be in the format of an email address
     */
    public function setContactEmail(?string $contactEmail): void
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * The license name used for the API
     */
    public function setLicenseName(?string $licenseName): void
    {
        $this->licenseName = $licenseName;
    }

    /**
     * A URL to the license used for the API. MUST be in the format of a URL
     */
    public function setLicenseUrl(?string $licenseUrl): void
    {
        $this->licenseUrl = $licenseUrl;
    }

    /**
     * Configuration details for a supported OAuth Flow
     */
    public function setAuthorizationFlow(string $name, int $flow, ?string $authorizationUrl, string $tokenUrl, ?string $refreshUrl = null, ?array $scopes = null): void
    {
        if (!isset($this->authFlows[$name])) {
            $this->authFlows[$name] = [];
        }

        $this->authFlows[$name][] = [$flow, $authorizationUrl, $tokenUrl, $refreshUrl, $scopes];
    }

    /**
     * Adds metadata to a single tag that is used by the Operation Object. It is
     * not mandatory to have a Tag Object per tag defined in the Operation
     * Object instances
     */
    public function addTag(string $name, string $description): void
    {
        $this->tags[$name] = $description;
    }
}
