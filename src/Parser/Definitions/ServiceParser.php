<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Address;
use Soap\WsdlReader\Model\Definitions\Port;
use Soap\WsdlReader\Model\Definitions\Service;
use Soap\WsdlReader\Model\Definitions\Services;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use Psl\Type;

class ServiceParser
{
    public function __invoke(Document $wsdl, DOMElement $service): Service
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new Service(
            name: $service->getAttribute('name'),
            port: new Port(
                name: $xpath->evaluate('string(./wsdl:port/@name)', Type\string(), $service),
                binding: $xpath->evaluate('string(./wsdl:port/@binding)', Type\string(), $service),
            ),
            address: new Address(
                location: $xpath->evaluate('string(./wsdl:port/soap:address/@location)', Type\string(), $service),
            ),
        );
    }

    public static function tryParse(Document $wsdl): Services
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        $parse = new self();

        return new Services(
            ...$xpath->query('/wsdl:definitions/wsdl:service')
                ->expectAllOfType(DOMElement::class)
                ->map(
                    fn (DOMElement $service) => $parse($wsdl, $service)
                )
        );
    }
}
