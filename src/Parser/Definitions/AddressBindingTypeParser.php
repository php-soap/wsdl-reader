<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use Dom\Element;
use Soap\WsdlReader\Model\Definitions\AddressBindingType;
use VeeWee\Xml\Dom\Document;

final class AddressBindingTypeParser
{
    public function __invoke(Document $wsdl, Element $namespacedElement): AddressBindingType
    {
        return AddressBindingType::from($namespacedElement->namespaceURI ?? '');
    }
}
