<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Api\Builder;

use PSX\Api\Operation;
use PSX\Api\OperationInterface;
use PSX\Schema\TypeInterface;

/**
 * ResourceBuilder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class OperationBuilder implements OperationBuilderInterface
{
    private Operation $operation;

    public function __construct(string $method, string $path, int $statusCode, TypeInterface $schema)
    {
        $this->operation = new Operation($method, $path, new Operation\Response($statusCode, $schema));
    }

    public function setDescription(string $description): self
    {
        $this->operation->setDescription($description);
        return $this;
    }

    public function addArgument(string $name, string $in, TypeInterface $schema): self
    {
        $this->operation->getArguments()->add($name, new Operation\Argument($in, $schema));
        return $this;
    }

    public function setAuthorization(bool $authorization): self
    {
        $this->operation->setAuthorization($authorization);
        return $this;
    }

    public function setSecurity(array $security): self
    {
        $this->operation->setSecurity($security);
        return $this;
    }

    public function setDeprecated(bool $deprecated): self
    {
        $this->operation->setDeprecated($deprecated);
        return $this;
    }

    public function addThrow(int $statusCode, TypeInterface $schema): self
    {
        $this->operation->addThrow(new Operation\Response($statusCode, $schema));
        return $this;
    }

    public function setTags(array $tags): self
    {
        $this->operation->setTags($tags);
        return $this;
    }

    public function getOperation(): OperationInterface
    {
        return $this->operation;
    }
}
