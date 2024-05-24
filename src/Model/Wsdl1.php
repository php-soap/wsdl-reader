<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model;

use GoetasWebservices\XML\XSDReader\Schema\Schema;
use Soap\WsdlReader\Model\Definitions\Bindings;
use Soap\WsdlReader\Model\Definitions\Messages;
use Soap\WsdlReader\Model\Definitions\Namespaces;
use Soap\WsdlReader\Model\Definitions\PortTypes;
use Soap\WsdlReader\Model\Definitions\Services;
use VeeWee\Xml\Xmlns\Xmlns;

final class Wsdl1
{
    public function __construct(
        public readonly Bindings $bindings,
        public readonly Messages $messages,
        public readonly PortTypes $portTypes,
        public readonly Services $services,
        public readonly Schema $schema,
        public readonly Namespaces $namespaces,
        public readonly ?Xmlns $targetNamespace,
    ) {
    }
}
