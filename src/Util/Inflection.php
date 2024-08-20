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

namespace PSX\Api\Util;

/**
 * Inflection
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Inflection
{
    /**
     * Transforms placeholder of an PSX route "/bar/:foo" into an curly bracket
     * "/bar/{foo}" route
     *
     * @param string $path
     * @return string
     */
    public static function convertPlaceholderToCurly(string $path): string
    {
        $path = preg_replace('/(\:|\*)(\w+)/i', '{$2}', $path);
        $path = preg_replace('/(\$)(\w+)(\<(.*)\>)/iU', '{$2}', $path);

        return $path;
    }

    /**
     * @param string $path
     * @return string
     */
    public static function convertPlaceholderToColon(string $path): string
    {
        $path = preg_replace('/(\{(\w+)\})/i', ':$2', $path);

        return $path;
    }

    /**
     * Returns an array containing the names of all variable path fragments
     */
    public static function extractPlaceholderNames(string $path): array
    {
        $parts = explode('/', $path);
        $result = [];

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            $name = null;
            if (str_starts_with($part, ':')) {
                $name = substr($part, 1);
            } elseif (str_starts_with($part, '$')) {
                $pos  = strpos($part, '<');
                $name = substr($part, 1, $pos - 1);
            } elseif (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $name = substr($part, 1, -1);
            } elseif (str_starts_with($part, '*')) {
                $name = substr($part, 1);
            }

            if ($name !== null) {
                $result[] = $name;
            }
        }

        return $result;
    }
}
