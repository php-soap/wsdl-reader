<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Psl\Type;
use Soap\WsdlReader\Model\Definitions\Operation;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;

final class OperationParser
{
    public function __invoke(Document $wsdl, DOMElement $operation): Operation
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new Operation(
            name: $xpath->evaluate('string(./@name)', Type\string(), $operation),
            input: OperationParamParser::tryParseOptionally($wsdl, 'input', $operation),
            output: OperationParamParser::tryParseOptionally($wsdl, 'output', $operation),
            fault: OperationParamParser::tryParseList(
                $wsdl,
                $xpath->query('./wsdl:fault', $operation)->expectAllOfType(DOMElement::class)
            ),
            documentation: $xpath->evaluate('string(./wsdl:documentation)', Type\string(), $operation),
        );
    }
}
