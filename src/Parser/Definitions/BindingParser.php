<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Binding;
use Soap\WsdlReader\Model\Definitions\BindingOperations;
use Soap\WsdlReader\Model\Definitions\Bindings;
use Soap\WsdlReader\Model\Definitions\QNamed;
use Soap\WsdlReader\Parser\Strategy\StrategySelector;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use function VeeWee\Xml\Dom\Locator\Element\locate_by_tag_name;

final class BindingParser
{
    public function __invoke(Document $wsdl, DOMElement $binding): Binding
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        $soapBinding = locate_by_tag_name($binding, 'binding')->expectFirst('Unable to locate the SOAP binding in a WSDL binding element!');
        $addressBindingType = (new AddressBindingTypeParser())($wsdl, $soapBinding);
        $strategy = (new StrategySelector())($addressBindingType);

        return new Binding(
            name: $binding->getAttribute('name'),
            type: QNamed::parse($binding->getAttribute('type')),
            addressBindingType: $addressBindingType,
            implementation: $strategy->parseBindingImplementation($wsdl, $soapBinding),
            operations: new BindingOperations(
                ...$xpath->query('./wsdl:operation', $binding)
                    ->expectAllOfType(DOMElement::class)
                    ->map(
                        static fn (DOMElement $operation) => (new BindingOperationParser())($wsdl, $operation, $strategy)
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
                    static fn (DOMElement $binding): Binding => $parse($wsdl, $binding)
                )
        );
    }
}
