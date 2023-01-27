<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Psl\Type;
use Soap\WsdlReader\Model\Definitions\Address;
use Soap\WsdlReader\Model\Definitions\Port;
use Soap\WsdlReader\Model\Definitions\QNamed;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use function VeeWee\Xml\Dom\Locator\Element\locate_by_tag_name;

final class PortParser
{
    public function __invoke(Document $wsdl, DOMElement $servicePort): Port
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        $address = locate_by_tag_name($servicePort, 'address')->expectFirst('Unable to locate an address section in a service port!');
        $type = (new AddressBindingTypeParser())($wsdl, $address);

        return new Port(
            name: $xpath->evaluate('string(./@name)', Type\string(), $servicePort),
            binding: QNamed::parse($xpath->evaluate('string(./@binding)', Type\string(), $servicePort)),
            address: new Address(
                type: $type,
                location: $xpath->evaluate('string(./@location)', Type\string(), $address),
            ),
        );
    }
}
