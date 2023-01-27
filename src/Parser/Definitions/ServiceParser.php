<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Port;
use Soap\WsdlReader\Model\Definitions\Ports;
use Soap\WsdlReader\Model\Definitions\Service;
use Soap\WsdlReader\Model\Definitions\Services;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;

final class ServiceParser
{
    public function __invoke(Document $wsdl, DOMElement $service): Service
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new Service(
            name: $service->getAttribute('name'),
            ports: new Ports(
                ...$xpath->query('./wsdl:port', $service)
                    ->expectAllOfType(DOMElement::class)
                    ->map(
                        static fn (DOMElement $servicePort): Port => (new PortParser())($wsdl, $servicePort)
                    )
            )
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
                    static fn (DOMElement $service) => $parse($wsdl, $service)
                )
        );
    }
}
