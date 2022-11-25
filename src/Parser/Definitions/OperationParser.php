<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Operation;
use Soap\WsdlReader\Model\Definitions\Param;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use Psl\Type;

class OperationParser
{
    public function __invoke(Document $wsdl, DOMElement $operation): Operation
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new Operation(
            name: $operation->getAttribute('name'),
            soapAction: $xpath->evaluate('string(./soap:operation/@soapAction)', Type\string(), $operation),   // TODO : Available in both binding and portType?
            style: $xpath->evaluate('string(./soap:operation/@style)', Type\string(), $operation),   // TODO : Available in both binding and portType?
            input: new Param(
                name: $xpath->evaluate('string(./wsdl:input/@name)', Type\string(), $operation),
                message: $xpath->evaluate('string(./wsdl:input/@message)', Type\string(), $operation),  // TODO : Available in both binding and portType?
                bodyUse: $xpath->evaluate('string(./wsdl:input/@name)', Type\string(), $operation),
            ),
            output: new Param( // TODO optional for oneway!
                name: $xpath->evaluate('string(./wsdl:output/@name)', Type\string(), $operation),
                message: $xpath->evaluate('string(./wsdl:output/@message)', Type\string(), $operation), // TODO : Available in both binding and portType?
                bodyUse: $xpath->evaluate('string(./wsdl:output/@name)', Type\string(), $operation),
            ),
        );
    }
}
