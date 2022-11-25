<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata;

use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Metadata;

final class WsdlMetadata implements Metadata
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
