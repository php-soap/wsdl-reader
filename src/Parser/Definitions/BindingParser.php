<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Psl\Type;
use Soap\WsdlReader\Model\Definitions\Binding;
use Soap\WsdlReader\Model\Definitions\BindingOperations;
use Soap\WsdlReader\Model\Definitions\Bindings;
use Soap\WsdlReader\Model\Definitions\QNamed;
use Soap\WsdlReader\Model\Definitions\TransportType;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use function Psl\invariant;
use function VeeWee\Xml\Dom\Locator\Element\locate_by_tag_name;

class BindingParser
{
    public function __invoke(Document $wsdl, DOMElement $binding)
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        $soapBinding = locate_by_tag_name($binding, 'binding')->first();
        invariant($soapBinding !== null, 'Unable to locate the SOAP binding in a WSDL binding element!');
        $soapVersion = (new SoapVersionParser())($wsdl, $soapBinding);

        return new Binding(
            name: $binding->getAttribute('name'),
            type: QNamed::parse($binding->getAttribute('type')),
            soapVersion: $soapVersion,
            transport: TransportType::from($xpath->evaluate('string(./@transport)', Type\string(), $soapBinding)),
            operations: new BindingOperations(
                ...$xpath->query('./wsdl:operation', $binding)
                    ->expectAllOfType(DOMElement::class)
                    ->map(
                        fn (DOMElement $operation) => (new BindingOperationParser())($wsdl, $operation, $soapVersion)
                    )
            ),
        );
    }

    public static function tryParse(Document $wsdl): Bindings
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        $parse = new self();

        return new Bindings(
            ...$xpath->query('/wsdl:definitions/wsdl:binding')
                ->expectAllOfType(DOMElement::class)
                ->map(
                    fn (DOMElement $binding) => $parse($wsdl, $binding)
                )
        );
    }
}
