<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\BindingOperation;
use Soap\WsdlReader\Model\Definitions\BindingStyle;
use Soap\WsdlReader\Model\Definitions\SoapVersion;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use Psl\Type;

class BindingOperationParser
{
    public function __invoke(Document $wsdl, DOMElement $operation, SoapVersion $soapVersion): BindingOperation
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        $soapVersionPrefix = $soapVersion->wsdlPresetName();

        return new BindingOperation(
            name: $operation->getAttribute('name'),
            soapVersion: $soapVersion,
            soapAction: $xpath->evaluate('string(./'.$soapVersionPrefix.':operation/@soapAction)', Type\string(), $operation),
            style: BindingStyle::from($xpath->evaluate('string(./'.$soapVersionPrefix.':operation/@style)', Type\string(), $operation)),
            input: BindingOperationMessageParser::tryParseFromOptionalSingleOperationMessage($wsdl, $operation, 'input', $soapVersion),
            output: BindingOperationMessageParser::tryParseFromOptionalSingleOperationMessage($wsdl, $operation, 'output', $soapVersion),
            fault: BindingOperationMessageParser::tryParseList($wsdl, $xpath->query('./wsdl:fault', $operation), $soapVersion),
        );
    }
}
