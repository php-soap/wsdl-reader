<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class BindingOperation
{
    public function __construct(
        public readonly string $name,
        public readonly SoapVersion $soapVersion,
        public readonly string $soapAction,
        public readonly BindingStyle $style,
        public readonly ?BindingOperationMessage $input,
        public readonly ?BindingOperationMessage $output,
        public readonly BindingOperationMessages $fault,
    ) {
    }
}
