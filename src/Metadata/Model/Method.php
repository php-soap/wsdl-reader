<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Model;

use Soap\WsdlReader\Metadata\Collection\ParameterCollection;

final class Method
{
    /**
     * @var ParameterCollection
     */
    private $parameters;

    /**
     * @var string
     */
    private $name;

    /**
     * @var XsdType
     */
    private $returnType;

    public function __construct(string $name, ParameterCollection $parameters, XsdType $returnType)
    {
        $this->name = $name;
        $this->returnType = $returnType;
        $this->parameters = $parameters;
    }

    public function getParameters(): ParameterCollection
    {
        return $this->parameters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): XsdType
    {
        return $this->returnType;
    }
}
