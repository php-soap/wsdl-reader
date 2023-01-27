<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions\Implementation\Operation;

use Soap\WsdlReader\Model\Definitions\BindingStyle;
use Soap\WsdlReader\Model\Definitions\SoapVersion;

final class SoapOperation implements OperationImplementation
{
    public function __construct(
        public readonly SoapVersion $version,
        public readonly string $action,
        public readonly BindingStyle $style,
    ) {
    }
}
