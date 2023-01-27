<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Soap\WsdlReader\Model\Definitions\Implementation\Operation\OperationImplementation;

final class BindingOperation
{
    public function __construct(
        public readonly string $name,
        public readonly OperationImplementation $implementation,
        public readonly ?BindingOperationMessage $input,
        public readonly ?BindingOperationMessage $output,
        public readonly BindingOperationMessages $fault,
    ) {
    }
}
