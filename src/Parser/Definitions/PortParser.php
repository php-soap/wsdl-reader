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
        $soapVersion = (new SoapVersionParser())($wsdl, $address);

        /*
         * TODO:
         * Addresses could also be HTTP 11 HTTP12 etc.
         * See PHP's implementation
         * https://github.com/php/php-src/blob/b9cd1cdb4f236b7e336b688b16d58a913f4d5c69/ext/soap/php_sdl.c#L787-L810
         *
         */

        return new Port(
            name: $xpath->evaluate('string(./@name)', Type\string(), $servicePort),
            binding: QNamed::parse($xpath->evaluate('string(./@binding)', Type\string(), $servicePort)),
            address: new Address(
                soapVersion: $soapVersion,
                location: $xpath->evaluate('string(./@location)', Type\string(), $address),
            ),
        );
    }
}
