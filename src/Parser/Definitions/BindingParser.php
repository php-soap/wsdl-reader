<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Binding;
use Soap\WsdlReader\Model\Definitions\Bindings;
use Soap\WsdlReader\Model\Definitions\Operations;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use Psl\Type;

class BindingParser
{
    public function __invoke(Document $wsdl, DOMElement $binding)
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new Binding(
            name: $binding->getAttribute('name'),
            type: $binding->getAttribute('type'),
            transport: $xpath->evaluate('string(./soap:binding/@transport)', Type\string(), $binding),
            operations: new Operations(
                ...$xpath->query('./wsdl:operation', $binding)
                    ->expectAllOfType(DOMElement::class)
                    ->map(
                        fn (DOMElement $operation) => (new OperationParser())($wsdl, $operation)
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
