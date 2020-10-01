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

namespace PSX\Api\Generator\Spec;

use Doctrine\Common\Annotations\Reader;
use PSX\Api\GeneratorAbstract;
use PSX\Schema\Parser\Popo\Dumper;

/**
 * OpenAPIAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class OpenAPIAbstract extends GeneratorAbstract
{
    const FLOW_AUTHORIZATION_CODE = 0;
    const FLOW_IMPLICIT = 1;
    const FLOW_PASSWORD = 2;
    const FLOW_CLIENT_CREDENTIALS = 3;

    /**
     * @var \PSX\Schema\Parser\Popo\Dumper
     */
    protected $dumper;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $tos;

    /**
     * @var string
     */
    protected $contactName;

    /**
     * @var string
     */
    protected $contactUrl;

    /**
     * @var string
     */
    protected $contactEmail;

    /**
     * @var string
     */
    protected $licenseName;

    /**
     * @var string
     */
    protected $licenseUrl;

    /**
     * @var array
     */
    protected $authFlows = [];

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @param \Doctrine\Common\Annotations\Reader $reader
     * @param integer $apiVersion
     * @param string $baseUri
     */
    public function __construct(Reader $reader, $apiVersion, $baseUri)
    {
        $this->dumper     = new Dumper($reader);
        $this->apiVersion = $apiVersion;
        $this->baseUri    = $baseUri;
        $this->authFlows  = [];
    }

    /**
     * The title of the application
     *
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * A short description of the application. CommonMark syntax MAY be used for
     * rich text representation
     *
     * @param string|null $description
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    /**
     * A URL to the Terms of Service for the API. MUST be in the format of a URL
     *
     * @param string|null $tos
     */
    public function setTermsOfService(?string $tos)
    {
        $this->tos = $tos;
    }

    /**
     * The identifying name of the contact person/organization
     *
     * @param string|null $contactName
     */
    public function setContactName(?string $contactName)
    {
        $this->contactName = $contactName;
    }

    /**
     * The URL pointing to the contact information. MUST be in the format of a
     * URL
     *
     * @param string|null $contactUrl
     */
    public function setContactUrl(?string $contactUrl)
    {
        $this->contactUrl = $contactUrl;
    }

    /**
     * The email address of the contact person/organization. MUST be in the
     * format of an email address
     *
     * @param string|null $contactEmail
     */
    public function setContactEmail(?string $contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * The license name used for the API
     *
     * @param string|null $licenseName
     */
    public function setLicenseName(?string $licenseName)
    {
        $this->licenseName = $licenseName;
    }

    /**
     * A URL to the license used for the API. MUST be in the format of a URL
     *
     * @param string|null $licenseUrl
     */
    public function setLicenseUrl(?string $licenseUrl)
    {
        $this->licenseUrl = $licenseUrl;
    }

    /**
     * Configuration details for a supported OAuth Flow
     * 
     * @param string $name
     * @param integer $flow
     * @param string $authorizationUrl
     * @param string $tokenUrl
     * @param string|null $refreshUrl
     * @param array|null $scopes
     */
    public function setAuthorizationFlow(string $name, int $flow, string $authorizationUrl, string $tokenUrl, ?string $refreshUrl = null, ?array $scopes = null)
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
     * 
     * @param string $name
     * @param string $description
     */
    public function addTag(string $name, string $description)
    {
        $this->tags[] = $this->newTag($name, $description);
    }

    /**
     * @param string $name
     * @param string $description
     * @return object
     */
    abstract protected function newTag(string $name, string $description);
}
