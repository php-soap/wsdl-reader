<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class BindingOperationMessage
{
    public function __construct(
        public readonly string $name,
        public readonly BindingUse $bindingUse,
    ) {}
}
