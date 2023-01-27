<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions\Implementation\Binding;

use Soap\WsdlReader\Model\Definitions\SoapVersion;
use Soap\WsdlReader\Model\Definitions\TransportType;

final class SoapBinding implements BindingImplementation
{
    public function __construct(
        public readonly SoapVersion $version,
        public readonly TransportType $transport,
    ) {
    }
}
