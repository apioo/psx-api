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

namespace PSX\Api\Console;

use PSX\Schema\Generator\Code\Chunks;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * OutputWriter
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OutputWriter
{
    /**
     * Writes the response to the output. In case the response represents
     * multiple files we format the response as HTTP multipart 
     * 
     * @param $response
     * @param OutputInterface $output
     */
    public static function write($response, OutputInterface $output)
    {
        if ($response instanceof Chunks) {
            $dir  = defined('PSX_PATH_CACHE') ? PSX_PATH_CACHE : sys_get_temp_dir();
            $file = tempnam($dir, 'sdk');

            $response->writeTo($file);

            $output->write(file_get_contents($file));
        } else {
            $output->write($response);
        }
    }
}
