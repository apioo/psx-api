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

namespace PSX\Api;

use PSX\Api\Exception\BreakingChangesException;
use PSX\Api\Exception\GeneratorException;
use PSX\Api\Exception\LockException;
use PSX\Api\Exception\ParserException;
use PSX\Api\Inspector\ChangelogGenerator;
use PSX\Schema\Inspector\SemVer;
use PSX\Schema\SchemaManagerInterface;

/**
 * The lock manager can help to ensure that your API does not introduce any breaking changes within a minor version.
 * Therefor you need to generate a lock file at the start of a major version, then you can verify every change
 * of the specification against this lock file and if a breaking change is introduced the verify method throws an exception
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class LockManager
{
    private ChangelogGenerator $changelogGenerator;

    public function __construct(private readonly SchemaManagerInterface $schemaManager)
    {
        $this->changelogGenerator = new ChangelogGenerator();
    }

    /**
     * @throws LockException
     */
    public function lock(SpecificationInterface $specification, string $lockFile): void
    {
        try {
            file_put_contents($lockFile, (string) (new Generator\Spec\TypeAPI())->generate($specification));
        } catch (GeneratorException $e) {
            throw new LockException('Could not generate lock file: ' . $e->getMessage(), previous: $e);
        }
    }

    /**
     * @throws LockException
     * @throws BreakingChangesException
     */
    public function verify(SpecificationInterface $specification, string $lockFile): void
    {
        if (!is_file($lockFile)) {
            throw new LockException('Provided lock file does not exist: ' . $lockFile);
        }

        try {
            $lockSpecification = (new Parser\TypeAPI($this->schemaManager))->parse(file_get_contents($lockFile));
        } catch (ParserException $e) {
            throw new LockException('Could not parse provided lock file ' . $lockFile . ' got: ' . $e->getMessage(), previous: $e);
        }

        $changelogs = $this->changelogGenerator->generate($lockSpecification, $specification);

        $breakingChanges = [];
        foreach ($changelogs as $level => $message) {
            if ($level === SemVer::MAJOR) {
                $breakingChanges[] = $message;
            }
        }

        if (count($breakingChanges) > 0) {
            throw new BreakingChangesException($breakingChanges);
        }
    }
}
