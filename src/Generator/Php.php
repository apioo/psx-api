<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Api\Generator;

use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter;
use PSX\Api\GeneratorAbstract;
use PSX\Api\Resource;
use PSX\Schema\Property;
use PSX\Schema\Builder;
use PSX\Schema\PropertyInterface;
use PSX\Schema\PropertySimpleAbstract;
use PSX\Schema\SchemaInterface;

/**
 * Php
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Php extends GeneratorAbstract
{
    /**
     * @var \PhpParser\BuilderFactory
     */
    protected $factory;

    /**
     * @var \PhpParser\PrettyPrinter\Standard
     */
    protected $printer;

    /**
     * @var string
     */
    protected $namespace;

    public function __construct($namespace = null)
    {
        $this->factory   = new BuilderFactory();
        $this->printer   = new PrettyPrinter\Standard();
        $this->namespace = $namespace === null ? 'PSX\Generation' : $namespace;
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    public function generate(Resource $resource)
    {
        $root      = $this->factory->namespace($this->namespace);
        $className = 'Endpoint';
        $methods   = $resource->getMethods();

        $class = $this->factory->class($className);
        $class->extend('SchemaApiAbstract');
        $class->setDocComment($this->getDocCommentForResource($resource));

        foreach ($methods as $methodName => $method) {
            $class->addStmt($this->factory->method('do' . ucfirst(strtolower($methodName)))
                ->makePublic()
                ->setDocComment($this->getDocCommentForMethod($method))
                ->addParam($this->factory->param('record'))
            );
        }

        $root->addStmt($this->factory->use('PSX\Framework\Controller\SchemaApiAbstract'));
        $root->addStmt($class->getNode());

        return $this->printer->prettyPrintFile([$root->getNode()]);
    }

    /**
     * @param \PSX\Api\Resource $resource
     * @return string
     */
    protected function getDocCommentForResource(Resource $resource)
    {
        $comment = '/**' . "\n";

        $title = $resource->getTitle();
        if (!empty($title)) {
            $comment.= ' * @Title("' . $this->escapeString($title) . '")' . "\n";
        }

        $description = $resource->getDescription();
        if (!empty($description)) {
            $comment.= ' * @Description("' . $this->escapeString($description) . '")' . "\n";
        }

        $parameters = $resource->getPathParameters()->getDefinition();
        foreach ($parameters as $name => $parameter) {
            $comment.= ' * @PathParam(' . $this->getParam($name, $parameter) . ')' . "\n";
        }

        $comment.= ' */';

        return $comment;
    }

    protected function getDocCommentForMethod(Resource\MethodAbstract $method)
    {
        $comment = '/**' . "\n";

        $description = $method->getDescription();
        if (!empty($description)) {
            $comment.= ' * @Description("' . $this->escapeString($description) . '")' . "\n";
        }

        $parameters = $method->getQueryParameters()->getDefinition();
        foreach ($parameters as $name => $parameter) {
            $comment.= ' * @QueryParam(' . $this->getParam($name, $parameter) . ')' . "\n";
        }

        $request = $method->getRequest();
        if ($request instanceof SchemaInterface) {
            $class   = $this->getClassNameForProperty($request->getDefinition());
            $comment.= ' * @Incoming(schema="' . $class . '")' . "\n";
        }

        $responses = $method->getResponses();
        foreach ($responses as $statusCode => $response) {
            if ($response instanceof SchemaInterface) {
                $class   = $this->getClassNameForProperty($response->getDefinition());
                $comment.= ' * @Outgoing(code=' . $statusCode . ', schema="' . $class . '")' . "\n";
            }
        }

        $comment.= ' */';

        return $comment;
    }

    protected function getParam($name, PropertySimpleAbstract $property)
    {
        $attributes = [
            'name' => '"' . $this->escapeString($name) . '"', 
            'type' => '"' . $this->escapeString($property->getTypeName()) . '"',
        ];

        $description = $property->getDescription();
        if (!empty($description)) {
            $attributes['description'] = '"' . $this->escapeString($description) . '"';
        }

        $required = $property->isRequired();
        if ($required !== null) {
            $attributes['required'] = $required ? 'true' : 'false';
        }

        $pattern = $property->getPattern();
        if (!empty($pattern)) {
            $attributes['pattern'] = '"' . $this->escapeString($pattern) . '"';
        }

        $enum = $property->getEnumeration();
        if (!empty($enum)) {
            $vals = [];
            foreach ($enum as $value) {
                $vals[] = '"' . $this->escapeString($value) . '"';
            }
            $attributes['enum'] = '{' . implode(', ', $vals) . '}';
        }

        $param = [];
        foreach ($attributes as $name => $value) {
            $param[] = $name . '=' . $value;
        }

        return implode(', ', $param);
    }

    protected function getClassNameForProperty(Property\ComplexType $property)
    {
        return $this->namespace . '\\' . ucfirst($property->getTypeName() . substr($property->getId(), 0, 8));
    }

    protected function escapeString($data)
    {
        return $data;
    }
}
