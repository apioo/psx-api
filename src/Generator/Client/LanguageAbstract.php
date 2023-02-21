<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Generator\Client;

use PSX\Api\Generator\Client\Dto\Exception;
use PSX\Api\Generator\Client\Dto\Tag;
use PSX\Api\Generator\Client\Util\Naming;
use PSX\Api\GeneratorInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;
use PSX\Schema\Schema;
use PSX\Schema\TypeFactory;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * LanguageAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class LanguageAbstract implements GeneratorInterface
{
    protected string $baseUrl;
    protected ?string $namespace;
    protected Environment $engine;

    /**
     * @var SchemaGeneratorInterface&Generator\NormalizerAwareInterface&Generator\TypeAwareInterface
     */
    protected SchemaGeneratorInterface $generator;

    private Naming $naming;
    private LanguageBuilder $converter;

    public function __construct(string $baseUrl, ?string $namespace = null)
    {
        $this->baseUrl   = $baseUrl;
        $this->namespace = $namespace;
        $this->engine    = $this->newTemplateEngine();
        $this->generator = $this->newGenerator();

        if (!$this->generator instanceof Generator\TypeAwareInterface) {
            throw new \RuntimeException('A schema generator must implement the TypeAwareInterface interface');
        }

        if (!$this->generator instanceof Generator\NormalizerAwareInterface) {
            throw new \RuntimeException('A schema generator must implement the NormalizerAwareInterface interface');
        }

        $this->naming = new Naming($this->generator->getNormalizer());
        $this->converter = new LanguageBuilder($this->generator, $this->naming);
    }

    /**
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function generate(SpecificationInterface $specification): Generator\Code\Chunks
    {
        $chunks = new Generator\Code\Chunks();

        $client = $this->converter->getClient($specification);

        foreach ($client->tags as $tag) {
            $imports = [];
            foreach ($tag->operations as $operation) {
                $imports = array_merge($imports, $operation->imports);
            }
            sort($imports);

            /** @var Tag $tag */
            $operations = $this->engine->render($this->getOperationTemplate(), [
                'operations' => $tag->operations,
            ]);

            $code = $this->engine->render($this->getTagTemplate(), [
                'namespace' => $this->namespace,
                'className' => $tag->className,
                'operations' => $operations,
                'imports' => $imports,
            ]);

            $chunks->append($this->getFileName($tag->className), $this->getFileContent($code, $tag->className));
        }

        foreach ($client->exceptions as $exception) {
            /** @var Exception $exception */
            $code = $this->engine->render($this->getExceptionTemplate(), [
                'namespace' => $this->namespace,
                'className' => $exception->className,
                'type' => $exception->type,
                'message' => $exception->message,
            ]);

            $chunks->append($this->getFileName($exception->className), $this->getFileContent($code, $exception->className));
        }

        $operations = '';
        $imports = [];
        if (count($client->operations) > 0) {
            foreach ($client->operations as $operation) {
                $imports = array_merge($imports, $operation->imports);
            }
            sort($imports);

            $operations = $this->engine->render($this->getOperationTemplate(), [
                'operations' => $client->operations,
            ]);
        }

        $code = $this->engine->render($this->getClientTemplate(), [
            'baseUrl' => $this->baseUrl,
            'namespace' => $this->namespace,
            'className' => $client->className,
            'security' => $client->security,
            'tags' => $client->tags,
            'operations' => $operations,
            'imports' => $imports,
        ]);

        $chunks->append($this->getFileName($client->className), $this->getFileContent($code, $client->className));

        $this->generateSchema($specification->getDefinitions(), $chunks);

        return $chunks;
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

    protected function getFileContent(string $code, string $identifier): string
    {
        return $code;
    }

    abstract protected function getOperationTemplate(): string;

    abstract protected function getTagTemplate(): string;
    abstract protected function getExceptionTemplate(): string;

    abstract protected function getClientTemplate(): string;

    /**
     * @return SchemaGeneratorInterface&Generator\TypeAwareInterface&Generator\NormalizerAwareInterface
     */
    abstract protected function newGenerator(): SchemaGeneratorInterface;

    abstract protected function getFileExtension(): string;

    protected function getFileName(string $identifier): string
    {
        $identifier = $this->generator->getNormalizer()->file($identifier);

        return $identifier . '.' . $this->getFileExtension();
    }

    private function newTemplateEngine(): Environment
    {
        $loader = new FilesystemLoader([__DIR__ . '/Language']);
        $engine = new Environment($loader);

        return $engine;
    }
}
