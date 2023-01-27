<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Soap\WsdlReader\Model\Definitions\Implementation\Binding\BindingImplementation;

final class Binding
{
    public function __construct(
        public readonly string $name,
        public readonly QNamed $type,
        public readonly AddressBindingType $addressBindingType,
        public readonly BindingImplementation $implementation,
        public readonly BindingOperations $operations,
    ) {
    }
}
