<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Metadata;

use Soap\WsdlReader\Metadata\Collection\MethodCollection;
use Soap\WsdlReader\Metadata\Collection\TypeCollection;

class Metadata implements MetadataInterface
{
    public function __construct(
        private TypeCollection $types,
        private MethodCollection $methods,
    ) {
    }

    public function getTypes(): TypeCollection
    {
        return $this->types;
    }

    public function getMethods(): MethodCollection
    {
        return $this->methods;
    }
}
