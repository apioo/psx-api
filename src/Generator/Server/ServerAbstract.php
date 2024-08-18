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

use PSX\Api\Exception\InvalidTypeException;
use PSX\Api\Generator\Client\Util\Naming;
use PSX\Api\Generator\Server\Dto\Context;
use PSX\Api\Generator\Server\Dto\File;
use PSX\Api\Generator\Server\Dto\Folder;
use PSX\Api\GeneratorInterface;
use PSX\Api\OperationInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Exception\TypeNotFoundException;
use PSX\Schema\Generator;
use PSX\Schema\Generator\Code\Chunks;
use PSX\Schema\Generator\Normalizer\NormalizerInterface;
use PSX\Schema\Generator\NormalizerAwareInterface;
use PSX\Schema\Generator\Type;
use PSX\Schema\Generator\TypeAwareInterface;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;
use PSX\Schema\Schema;
use PSX\Schema\Type\AnyType;
use PSX\Schema\Type\MapType;
use PSX\Schema\Type\ReferenceType;
use PSX\Schema\Type\StructType;
use PSX\Schema\TypeFactory;
use PSX\Schema\TypeInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * ServerAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class ServerAbstract implements GeneratorInterface
{
    private string $templateDir;
    private Environment $templateEngine;
    private SchemaGeneratorInterface $generator;
    private Type\GeneratorInterface $typeGenerator;
    protected NormalizerInterface $normalizer;
    protected Naming $naming;

    public function __construct()
    {
        $this->templateDir = __DIR__ . '/Template/' . $this->getTemplateDir();

        $this->templateEngine = new Environment(new FilesystemLoader($this->templateDir), [
            'cache' => false,
        ]);
        $this->generator = $this->newGenerator();

        if ($this->generator instanceof TypeAwareInterface) {
            $this->typeGenerator = $this->generator->getTypeGenerator();
        } else {
            throw new \RuntimeException('Provided generator is not type aware');
        }

        if ($this->generator instanceof NormalizerAwareInterface) {
            $this->normalizer = $this->generator->getNormalizer();
        } else {
            throw new \RuntimeException('Provided generator is not normalizer aware');
        }

        $this->naming = new Naming($this->normalizer);
    }

    public function generate(SpecificationInterface $specification): Chunks|string
    {
        $context = $this->buildContext($specification);
        $folder = $this->buildFolderStructure($specification);
        $chunks = $this->copyFiles($this->templateDir, $context);

        $controllerChunks = $chunks->findByPath($this->getControllerPath());
        if (!$controllerChunks instanceof Chunks) {
            throw new \RuntimeException('Could not find configured controller path');
        }

        $this->generateRecursive($folder, $controllerChunks, $specification);

        $modelChunks = $chunks->findByPath($this->getModelPath());
        if (!$modelChunks instanceof Chunks) {
            throw new \RuntimeException('Could not find configured model path');
        }

        $this->generateSchema($specification->getDefinitions(), $modelChunks);

        return $chunks;
    }

    /**
     * @return SchemaGeneratorInterface&Generator\TypeAwareInterface&Generator\NormalizerAwareInterface
     */
    abstract protected function newGenerator(): SchemaGeneratorInterface;

    private function generateRecursive(Folder $folder, Chunks $chunks, SpecificationInterface $specification): void
    {
        foreach ($folder->getFolders() as $name => $child) {
            $result = $chunks->getChunk($name);

            if (!$result instanceof Chunks) {
                $result = new Chunks();
                $chunks->append($this->buildFolderName($name), $result);
            }

            $this->generateRecursive($child, $result, $specification);
        }

        foreach ($folder->getFiles() as $name => $file) {
            $content = $this->generateControllerFile($file, $specification);

            $chunks->append($this->buildControllerFileName($name) . '.' . $this->getFileExtension(), $content);
        }
    }

    abstract protected function getControllerPath(): string;
    abstract protected function getModelPath(): string;
    abstract protected function buildControllerFileName(string $name): string;
    abstract protected function generateControllerFile(File $file, SpecificationInterface $specification): string;
    abstract protected function getFileExtension(): string;

    protected function buildFolderName(string $name): string
    {
        return $name;
    }

    protected function buildFolderStructure(SpecificationInterface $specification): Folder
    {
        $folder = new Folder();
        $operations = $specification->getOperations();
        foreach ($operations->getAll() as $operationId => $operation) {
            $this->buildRecursive($folder, explode('.', $operationId), $operation);
        }

        return $folder;
    }

    /**
     * @throws InvalidTypeException
     * @throws TypeNotFoundException
     */
    protected function newType(TypeInterface $type, DefinitionsInterface $definitions): Dto\Type
    {
        if ($type instanceof ReferenceType) {
            // in case we have a reference type we take a look at the reference, normally this is a struct type but in
            // some special cases we need to extract the type
            $refType = $definitions->getType($type->getRef());
            if ($refType instanceof ReferenceType) {
                $refType = $definitions->getType($refType->getRef());
            }

            if (!$refType instanceof StructType && !$refType instanceof MapType && !$refType instanceof AnyType) {
                throw new InvalidTypeException('A reference can only point to a struct or map type, got: ' . get_class($refType) . ' for reference: ' . $type->getRef());
            }
        }

        return new Dto\Type(
            $this->typeGenerator->getType($type),
            $this->typeGenerator->getDocType($type),
        );
    }

    protected function generateSchema(DefinitionsInterface $definitions, Generator\Code\Chunks $chunks): void
    {
        $schema = new Schema(TypeFactory::getAny(), $definitions);
        $result = $this->generator->generate($schema);

        if ($result instanceof Generator\Code\Chunks) {
            foreach ($result->getChunks() as $identifier => $code) {
                $chunks->append($this->getFileName($identifier), $this->getFileContent($code, $identifier));
            }
        } else {
            $chunks->append($this->getFileName('RootSchema'), $result);
        }
    }

    protected function getFileName(string $identifier): string
    {
        $identifier = $this->generator->getNormalizer()->file($identifier);

        return $identifier . '.' . $this->getFileExtension();
    }

    protected function getFileContent(string $code, string $identifier): string
    {
        return $code;
    }

    private function buildRecursive(Folder $parent, array $operationId, OperationInterface $operation): void
    {
        if (count($operationId) === 1 || count($operationId) === 2) {
            $fileName = $operationId[0] ?? null;
            $method = $operationId[1] ?? null;

            if ($method === null) {
                $method = $fileName;
                $fileName = 'app';
            }

            $file = $parent->getFile($fileName);
            if ($file === null) {
                $file = new File($fileName);
                $parent->addFile($fileName, $file);
            }

            $file->addOperation($method, $operation);
        } elseif (count($operationId) > 2) {
            $name = $operationId[0] ?? null;
            unset($operationId[0]);

            $child = $parent->getFolder($name);
            if ($child === null) {
                $child = new Folder();
                $parent->addFolder($name, $child);
            }

            $this->buildRecursive($child, array_values($operationId), $operation);
        }
    }

    private function buildContext(SpecificationInterface $specification): Context
    {
        $context = new Context();

        return $context;
    }

    private function copyFiles(string $templateDir, Context $context): Chunks
    {
        $chunks = new Chunks();
        $files = scandir($templateDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.gitkeep') {
                continue;
            }

            $templatePath = $templateDir . '/' . $file;

            if (is_dir($templatePath)) {
                $result = $this->copyFiles($templatePath, $context);

                $chunks->append($file, $result);
            } elseif (is_file($templatePath)) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                if ($extension === 'twig') {
                    $file = substr($file, 0, -5);
                    $result = $this->templateEngine->render(substr($templatePath, strlen($this->templateDir)), $context->getArrayCopy());
                } else {
                    $result = file_get_contents($templatePath);
                }

                $chunks->append($file, $result);
            }
        }

        return $chunks;
    }

    private function getTemplateDir(): string
    {
        $parts = explode('\\', static::class);
        $lastKey = array_key_last($parts);

        return $parts[$lastKey];
    }
}
