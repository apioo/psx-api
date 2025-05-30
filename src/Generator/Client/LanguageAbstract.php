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

namespace PSX\Api\Generator\Client;

use PSX\Api\Generator\Client\Dto\Response;
use PSX\Api\Generator\Client\Dto\Tag;
use PSX\Api\Generator\Client\Util\Naming;
use PSX\Api\Generator\ConfigurationAwareInterface;
use PSX\Api\Generator\ConfigurationTrait;
use PSX\Api\GeneratorInterface;
use PSX\Api\SpecificationInterface;
use PSX\Schema\DefinitionsInterface;
use PSX\Schema\Generator;
use PSX\Schema\GeneratorInterface as SchemaGeneratorInterface;
use PSX\Schema\Schema;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

/**
 * LanguageAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class LanguageAbstract implements GeneratorInterface, ConfigurationAwareInterface
{
    use ConfigurationTrait;

    protected ?string $namespace;
    protected array $mapping;
    protected ?Generator\Config $config;
    protected Environment $engine;

    /**
     * @var SchemaGeneratorInterface&Generator\NormalizerAwareInterface&Generator\TypeAwareInterface
     */
    protected SchemaGeneratorInterface $generator;

    private Naming $naming;
    private LanguageBuilder $converter;

    public function __construct(?string $baseUrl = null, ?Generator\Config $config = null)
    {
        $this->baseUrl = $baseUrl;
        $this->namespace = $config?->get(Generator\Config::NAMESPACE);
        $this->mapping = (array) $config?->get(Generator\Config::MAPPING);
        $this->config = $config;
        $this->engine = $this->newTemplateEngine();
        $this->generator = $this->newGenerator();

        if (!$this->generator instanceof Generator\TypeAwareInterface) {
            throw new \RuntimeException('A schema generator must implement the TypeAwareInterface interface');
        }

        if (!$this->generator instanceof Generator\NormalizerAwareInterface) {
            throw new \RuntimeException('A schema generator must implement the NormalizerAwareInterface interface');
        }

        $this->naming = new Naming($this->generator->getNormalizer());
        $this->converter = new LanguageBuilder($this->generator, $this->naming, $this->mapping);
    }

    /**
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function generate(SpecificationInterface $specification): Generator\Code\Chunks
    {
        $chunks = new Generator\Code\Chunks();

        $baseUrl = $this->getBaseUrl($specification);
        $security = $this->getSecurity($specification);
        $client = $this->converter->getClient($specification, $baseUrl, $security);
        $tagImports = [];

        foreach ($client->tags as $tag) {
            $this->buildTag($tag, $chunks);

            $tagImports[$this->generator->getNormalizer()->file($tag->className)] = $tag->className;
        }

        foreach ($client->exceptions as $exception) {
            $code = $this->engine->render($this->getExceptionTemplate(), [
                'namespace' => $this->namespace,
                'className' => $exception->className,
                'schema' => $exception->schema,
                'message' => $exception->message,
                'imports' => $exception->imports,
            ]);

            $chunks->append($this->getFileName($exception->className), $this->getFileContent($code, $exception->className));
        }

        $imports = $tagImports;
        $operations = '';
        if (count($client->operations) > 0) {
            foreach ($client->operations as $operation) {
                $imports = array_merge($imports, $operation->imports);
            }

            asort($imports);

            $operations = $this->engine->render($this->getOperationTemplate(), [
                'className' => $client->className,
                'operations' => $client->operations,
            ]);
        }

        $code = $this->engine->render($this->getClientTemplate(), [
            'baseUrl' => $client->baseUrl ?? $this->baseUrl,
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
        $result = $this->generator->generate(new Schema($definitions, null));

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

    protected function getTemplateDir(): string
    {
        return __DIR__ . '/Language';
    }

    private function buildTag(Tag $tag, Generator\Code\Chunks $chunks): void
    {
        $imports = [];
        if (!empty($tag->tags)) {
            foreach ($tag->tags as $subTag) {
                $this->buildTag($subTag, $chunks);

                $imports[$this->generator->getNormalizer()->file($subTag->className)] = $subTag->className;
            }
        }

        if (!empty($tag->operations)) {
            foreach ($tag->operations as $operation) {
                $imports = array_merge($imports, $operation->imports);
            }

            $operations = $this->engine->render($this->getOperationTemplate(), [
                'className' => $tag->className,
                'operations' => $tag->operations,
            ]);
        } else {
            $operations = '';
        }

        asort($imports);

        $code = $this->engine->render($this->getTagTemplate(), [
            'namespace' => $this->namespace,
            'className' => $tag->className,
            'tags' => $tag->tags,
            'operations' => $operations,
            'imports' => $imports,
        ]);

        $chunks->append($this->getFileName($tag->className), $this->getFileContent($code, $tag->className));
    }

    private function newTemplateEngine(): Environment
    {
        $twig = new Environment(new FilesystemLoader([$this->getTemplateDir()]));
        $twig->addFilter(new TwigFilter('throws_unique', function(array $values){
            $map = [];
            foreach ($values as $code => $response) {
                if ($response instanceof Response) {
                    $map[$code] = $response->schema->type;
                }
            }

            $result = [];
            foreach (array_unique($map) as $code => $type) {
                $result[$code] = $values[$code];
            }
            return $result;
        }));
        return $twig;
    }
}
