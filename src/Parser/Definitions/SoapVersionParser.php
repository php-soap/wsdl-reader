<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\SoapVersion;
use Soap\Xml\Xmlns;
use VeeWee\Xml\Dom\Document;

final class SoapVersionParser
{
    public function __invoke(Document $wsdl, DOMElement $soapNamespacedElement): SoapVersion
    {
        return match ($soapNamespacedElement->namespaceURI ?? '') {
            Xmlns::soap()->value() => SoapVersion::SOAP_11,
            Xmlns::soap12()->value() => SoapVersion::SOAP_12,
        };
    }
}
