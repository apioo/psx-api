<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2024 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Generator\Server;

use PSX\Api\Generator\Server\Dto\File;
use PSX\Api\Operation\ArgumentInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;

/**
 * PHP
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class PHP extends ServerAbstract
{
    protected function newGenerator(): SchemaGeneratorInterface
    {
        return new Generator\Php();
    }

    protected function getControllerPath(): string
    {
        return 'src/Controller';
    }

    protected function getModelPath(): string
    {
        return 'src/Model';
    }

    protected function buildControllerFileName(string $name): string
    {
        return $name;
    }

    protected function getFileExtension(): string
    {
        return 'php';
    }

    protected function generateControllerFile(File $file, SpecificationInterface $specification): string
    {
        $controllerClass = $this->buildControllerClass($file);

        $controller = '<?php' . "\n";
        $controller.= "\n";
        $controller.= 'namespace App\Controller;' . "\n";
        $controller.= "\n";
        $controller.= 'class ' . $controllerClass . ' extends ControllerAbstract' . "\n";
        $controller.= '{' . "\n";

        foreach ($file->getOperations() as $operationName => $operation) {
            $method = ucfirst(strtolower($operation->getMethod()));

            $args = [];
            foreach ($operation->getArguments()->getAll() as $argumentName => $argument) {
                if ($argument->getIn() === ArgumentInterface::IN_PATH) {
                    $type = $this->newType($argument->getSchema(), $specification->getDefinitions());
                    $args[] = '#[Param(\'' . $argumentName . '\')] ' . $type->type . ' $' . $this->normalizer->argument($argumentName);
                } elseif ($argument->getIn() === ArgumentInterface::IN_QUERY) {
                    $type = $this->newType($argument->getSchema(), $specification->getDefinitions());
                    $args[] = '#[Query(\'' . $argumentName . '\')] ' . $type->type . ' $' . $this->normalizer->argument($argumentName);
                } elseif ($argument->getIn() === ArgumentInterface::IN_HEADER) {
                    $type = $this->newType($argument->getSchema(), $specification->getDefinitions());
                    $args[] = '#[Header(\'' . $argumentName . '\')] ' . $type->type . ' $' . $this->normalizer->argument($argumentName);
                } elseif ($argument->getIn() === ArgumentInterface::IN_BODY) {
                    $type = $this->newType($argument->getSchema(), $specification->getDefinitions());
                    $args[] = '#[Body] ' . $type->type . ' $' . $this->normalizer->argument($argumentName);
                }
            }

            $type = $this->newType($operation->getReturn()->getSchema(), $specification->getDefinitions());

            $controller.= '  #[' . $method . ']' . "\n";
            $controller.= '  #[Path(\'' . $operation->getPath() . '\')]' . "\n";
            $controller.= '  #[StatusCode(' . $operation->getReturn()->getCode() . ')]' . "\n";
            $controller.= '  public function ' . $operationName . '(' . implode(', ', $args) . '): ' . $type->type . ' {' . "\n";
            $controller.= '    // @TODO implement method' . "\n";
            $controller.= '  }' . "\n";
            $controller.= "\n";
        }

        $controller.= '}' . "\n";

        return $controller;
    }

    private function buildControllerClass(File $file): string
    {
        return $this->normalizer->class($file->getName());
    }
}
