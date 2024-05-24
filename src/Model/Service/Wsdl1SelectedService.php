<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Service;

use Soap\WsdlReader\Model\Definitions\Binding;
use Soap\WsdlReader\Model\Definitions\Messages;
use Soap\WsdlReader\Model\Definitions\Namespaces;
use Soap\WsdlReader\Model\Definitions\Port;
use Soap\WsdlReader\Model\Definitions\PortType;
use Soap\WsdlReader\Model\Definitions\Service;
use Soap\WsdlReader\Model\Wsdl1;

final class Wsdl1SelectedService
{
    public function __construct(
        public readonly Wsdl1 $wsdl,
        public readonly Service $service,
        public readonly Port $port,
        public readonly Binding $binding,
        public readonly PortType $portType,
        public readonly Messages $messages,
        public readonly Namespaces $namespaces,
    ) {
    }
}
