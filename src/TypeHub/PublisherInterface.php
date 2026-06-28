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

namespace PSX\Api\TypeHub;

use PSX\Api\Exception\GeneratorException;
use PSX\Api\Exception\PublishException;

/**
 * PublisherInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface PublisherInterface
{
    /**
     * Returns the TypeHub specification which we will publish, can be used to preview the spec
     *
     * @throws GeneratorException
     */
    public function get(?string $filterName = null, bool $standalone = false): string;

    /**
     * Sends the specification to the TypeHub platform
     *
     * @throws PublishException
     */
    public function publish(string $name, string $clientId, string $clientSecret, ?string $filterName = null, bool $standalone = false): void;

    /**
     * Generates a changelog which shows the diff between the current version and the latest tag
     *
     * @throws PublishException
     */
    public function changelog(string $name, string $clientId, string $clientSecret): Changelog;

    /**
     * Creates a tag for the current version
     *
     * @throws PublishException
     */
    public function tag(string $name, string $clientId, string $clientSecret): void;

}
