<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\AddressBindingType;
use VeeWee\Xml\Dom\Document;

final class AddressBindingTypeParser
{
    public function __invoke(Document $wsdl, DOMElement $namespacedElement): AddressBindingType
    {
        return AddressBindingType::from($namespacedElement->namespaceURI ?? '');
    }
}
