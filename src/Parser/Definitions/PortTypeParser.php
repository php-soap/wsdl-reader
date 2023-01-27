<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Operations;
use Soap\WsdlReader\Model\Definitions\PortType;
use Soap\WsdlReader\Model\Definitions\PortTypes;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;

final class PortTypeParser
{
    public function __invoke(Document $wsdl, DOMElement $portType): PortType
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new PortType(
            name: $portType->getAttribute('name'),
            operations: new Operations(
                ...$xpath->query('./wsdl:operation', $portType)
                    ->expectAllOfType(DOMElement::class)
                    ->map(
                        static fn (DOMElement $operation) => (new OperationParser())($wsdl, $operation)
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
                ->expectAllOfType(DOMElement::class)
                ->map(
                    static fn (DOMElement $portType) => $parse($wsdl, $portType)
                )
        );
    }
}
