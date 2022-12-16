<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class SelectedService
{
    public function __construct(
        public readonly Service $service,
        public readonly Port $port,
        public readonly Binding $binding,
        public readonly PortType $portType,
        public readonly Messages $messages,
        public readonly Namespaces $namespaces,
    ){
    }
}
