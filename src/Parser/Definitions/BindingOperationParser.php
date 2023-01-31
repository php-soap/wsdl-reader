<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\BindingOperation;
use Soap\WsdlReader\Parser\Strategy\StrategyInterface;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use function VeeWee\Xml\Dom\Locator\Element\locate_by_tag_name;

final class BindingOperationParser
{
    public function __invoke(Document $wsdl, DOMElement $operation, StrategyInterface $strategy): BindingOperation
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        $soapOperation = locate_by_tag_name($operation, 'operation')->expectFirst('Unable to locate the operation implementation in a WSDL operation element!');

        return new BindingOperation(
            name: $operation->getAttribute('name'),
            implementation: $strategy->parseOperationImplementation($wsdl, $soapOperation),
            input: BindingOperationMessageParser::tryParseFromOptionalSingleOperationMessage($wsdl, $operation, 'input', $strategy),
            output: BindingOperationMessageParser::tryParseFromOptionalSingleOperationMessage($wsdl, $operation, 'output', $strategy),
            fault: BindingOperationMessageParser::tryParseList(
                $wsdl,
                $xpath->query('./wsdl:fault', $operation)->expectAllOfType(DOMElement::class),
                $strategy
            ),
        );
    }
}
