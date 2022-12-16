<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Operation;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use Psl\Type;

class OperationParser
{
    public function __invoke(Document $wsdl, DOMElement $operation): Operation
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new Operation(
            input: OperationParamParser::tryParseOptionally($wsdl, 'input', $operation),
            output: OperationParamParser::tryParseOptionally($wsdl, 'output', $operation),
            fault: OperationParamParser::tryParseList($wsdl, $xpath->query('./wsdl:fault', $operation)),
            documentation: $xpath->evaluate('string(./wsdl:documentation)', Type\string(), $operation),
        );
    }
}
