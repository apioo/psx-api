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
use PSX\Api\OperationInterface;
use PSX\Schema\ContentType;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;
use PSX\Schema\Type\PropertyTypeAbstract;
use PSX\Schema\Type\ReferencePropertyType;

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
        $config = new Generator\Config();
        $config->put(Generator\Config::NAMESPACE, 'App\\Model');

        return new Generator\Php($config);
    }

    protected function getControllerPath(): string
    {
        return 'src/Controller';
    }

    protected function getModelPath(): string
    {
        return 'src/Model';
    }

    protected function getFileExtension(): string
    {
        return 'php';
    }

    protected function getFileContent(string $code, string $identifier): string
    {
        $comment = '/**' . "\n";
        $comment.= ' * ' . $identifier . ' automatically generated by SDKgen please do not edit this file manually' . "\n";
        $comment.= ' * @see https://sdkgen.app' . "\n";
        $comment.= ' */' . "\n";

        return '<?php' . "\n" . $comment . "\n" . $code;
    }

    protected function buildControllerFileName(string $name): string
    {
        return $this->normalizer->file($name);
    }

    protected function buildFolderName(string $name): string
    {
        return $this->normalizer->file($name);
    }

    protected function generateHeader(File $file, array $imports): string
    {
        $namespace = ['App', 'Controller'];
        $folder = $file->getFolder();
        while ($folder !== null && $folder->getName() !== '.') {
            $namespace[] = $this->normalizer->class($folder->getName());
            $folder = $folder->getParent();
        }

        $controllerClass = $this->normalizer->class($file->getName());

        $controller = 'namespace ' . implode('\\', $namespace). ';' . "\n";
        $controller.= "\n";
        $controller.= 'use App\Model;' . "\n";
        $controller.= 'use PSX\Api\Attribute\Body;' . "\n";
        $controller.= 'use PSX\Api\Attribute\Delete;' . "\n";
        $controller.= 'use PSX\Api\Attribute\Get;' . "\n";
        $controller.= 'use PSX\Api\Attribute\Param;' . "\n";
        $controller.= 'use PSX\Api\Attribute\Patch;' . "\n";
        $controller.= 'use PSX\Api\Attribute\Path;' . "\n";
        $controller.= 'use PSX\Api\Attribute\Post;' . "\n";
        $controller.= 'use PSX\Api\Attribute\Put;' . "\n";
        $controller.= 'use PSX\Api\Attribute\Query;' . "\n";
        $controller.= 'use PSX\Api\Attribute\StatusCode;' . "\n";
        $controller.= 'use PSX\Framework\Controller\ControllerAbstract;' . "\n";
        $controller.= "\n";
        $controller.= 'class ' . $controllerClass . ' extends ControllerAbstract' . "\n";
        $controller.= '{' . "\n";

        return $controller;
    }

    protected function generateFooter(File $file): string
    {
        $controller = '}' . "\n";

        return $controller;
    }

    protected function generateArgumentPath(string $rawName, string $variableName, string $type, PropertyTypeAbstract|ContentType $argumentType): string
    {
        if ($argumentType instanceof ReferencePropertyType) {
            $type = 'Model\\' . $type;
        }

        if ($rawName === $variableName) {
            return '#[Param] ' . $type . ' $' . $variableName;
        } else {
            return '#[Param(\'' . $rawName . '\')] ' . $type . ' $' . $variableName;
        }
    }

    protected function generateArgumentQuery(string $rawName, string $variableName, string $type, PropertyTypeAbstract|ContentType $argumentType): string
    {
        if ($argumentType instanceof ReferencePropertyType) {
            $type = 'Model\\' . $type;
        }

        if ($rawName === $variableName) {
            return '#[Query] ' . $type . ' $' . $variableName;
        } else {
            return '#[Query(\'' . $rawName . '\')] ' . $type . ' $' . $variableName;
        }
    }

    protected function generateArgumentHeader(string $rawName, string $variableName, string $type, PropertyTypeAbstract|ContentType $argumentType): string
    {
        if ($argumentType instanceof ReferencePropertyType) {
            $type = 'Model\\' . $type;
        }

        if ($rawName === $variableName) {
            return '#[Header] ' . $type . ' $' . $variableName;
        } else {
            return '#[Header(\'' . $rawName . '\')] ' . $type . ' $' . $variableName;
        }
    }

    protected function generateArgumentBody(string $variableName, string $type, PropertyTypeAbstract|ContentType $argumentType): string
    {
        if ($argumentType instanceof ReferencePropertyType) {
            $type = 'Model\\' . $type;
        }

        return '#[Body] ' . $type . ' $' . $variableName;
    }

    protected function generateMethod(string $operationName, OperationInterface $operation, array $arguments, string $type, PropertyTypeAbstract|ContentType $returnType): string
    {
        if ($returnType instanceof ReferencePropertyType) {
            $type = 'Model\\' . $type;
        }

        $methodName = ucfirst(strtolower($operation->getMethod()));

        $method = '    #[' . $methodName . ']' . "\n";
        $method.= '    #[Path(\'' . $operation->getPath() . '\')]' . "\n";
        $method.= '    #[StatusCode(' . $operation->getReturn()->getCode() . ')]' . "\n";
        $method.= '    public function ' . $operationName . '(' . implode(', ', $arguments) . '): ' . $type . "\n";
        $method.= '    {' . "\n";
        $method.= '        // @TODO implement method' . "\n";
        $method.= '    }' . "\n";
        $method.= "\n";

        return $method;
    }
}
