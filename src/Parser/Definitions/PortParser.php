<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Psl\Type;
use Soap\WsdlReader\Model\Definitions\Address;
use Soap\WsdlReader\Model\Definitions\Port;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use function Psl\invariant;
use function VeeWee\Xml\Dom\Locator\Element\locate_by_tag_name;

class PortParser
{
    public function __invoke(Document $wsdl, DOMElement $servicePort): Port
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        $address = locate_by_tag_name($servicePort, 'address')->first();
        invariant($address !== null, 'Unable to locate an address section in a service port!');
        $soapVersion = (new SoapVersionParser())($wsdl, $address);

        return new Port(
            name: $xpath->evaluate('string(./@name)', Type\string(), $servicePort),
            binding: $xpath->evaluate('string(./@binding)', Type\string(), $servicePort),
            address: new Address(
                soapVersion: $soapVersion,
                location: $xpath->evaluate('string(./@location)', Type\string(), $address),
            ),
        );
    }
}
