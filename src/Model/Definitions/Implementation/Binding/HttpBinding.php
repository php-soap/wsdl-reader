<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions\Implementation\Binding;

use Soap\WsdlReader\Model\Definitions\TransportType;

final class HttpBinding implements BindingImplementation
{
    public function __construct(
        public readonly string $verb,
        public readonly TransportType $transport,
    ) {
    }
}
