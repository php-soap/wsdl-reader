<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class BindingOperationMessage
{
    public function __construct(
        public readonly string $name,
        public readonly BindingUse $bindingUse,
    ) {
    }
}
