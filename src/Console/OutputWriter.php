<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2019 Christoph Kappestein <christoph.kappestein@gmail.com>
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
            $boundary = '85d62adb6dd029cc080b15eb2086a8e054887f8a';
            foreach ($response->getChunks() as $identifier => $code) {
                $output->writeln('--' . $boundary);
                $output->writeln('Content-Disposition: attachment; filename="' . $identifier . '"');
                $output->writeln('Content-Length: ' . strlen($code));
                $output->writeln('');
                $output->write($code);
                $output->writeln('');
            }
            $output->writeln('--' . $boundary . '--');
        } else {
            $output->write($response);
        }
    }
}
