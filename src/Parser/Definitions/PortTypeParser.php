<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use Dom\Element;
use Soap\WsdlReader\Model\Definitions\Operations;
use Soap\WsdlReader\Model\Definitions\PortType;
use Soap\WsdlReader\Model\Definitions\PortTypes;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;

final class PortTypeParser
{
    public function __invoke(Document $wsdl, Element $portType): PortType
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new PortType(
            name: $portType->getAttribute('name') ?? '',
            operations: new Operations(
                ...$xpath->query('./wsdl:operation', $portType)
                    ->expectAllOfType(Element::class)
                    ->map(
                        static fn (Element $operation) => (new OperationParser())($wsdl, $operation)
                    )
            ),
        );
    }

    public static function tryParse(Document $wsdl): PortTypes
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        $parse = new self();

        return new PortTypes(
            ...$xpath->query('/wsdl:definitions/wsdl:portType')
                ->expectAllOfType(Element::class)
                ->map(
                    static fn (Element $portType) => $parse($wsdl, $portType)
                )
        );
    }
}
