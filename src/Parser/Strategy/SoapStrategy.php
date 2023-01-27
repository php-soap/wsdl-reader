<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Strategy;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\BindingStyle;
use Soap\WsdlReader\Model\Definitions\BindingUse;
use Soap\WsdlReader\Model\Definitions\Implementation\Binding\BindingImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Binding\SoapBinding;
use Soap\WsdlReader\Model\Definitions\Implementation\Message\MessageImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Message\SoapMessage;
use Soap\WsdlReader\Model\Definitions\Implementation\Operation\OperationImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Operation\SoapOperation;
use Soap\WsdlReader\Model\Definitions\SoapVersion;
use Soap\WsdlReader\Model\Definitions\TransportType;
use Soap\WsdlReader\Parser\Definitions\SoapVersionParser;
use VeeWee\Xml\Dom\Document;
use function VeeWee\Xml\Dom\Locator\Element\locate_by_tag_name;

final class SoapStrategy implements StrategyInterface
{
    public function parseBindingImplementation(Document $wsdl, DOMElement $binding): BindingImplementation
    {
        return new SoapBinding(
            version: $this->parseVersionFromNode($wsdl, $binding),
            transport: TransportType::from($binding->getAttribute('transport'))
        );
    }

    public function parseOperationImplementation(Document $wsdl, DOMElement $operation): OperationImplementation
    {
        return new SoapOperation(
            version: $this->parseVersionFromNode($wsdl, $operation),
            action: $operation->getAttribute('soapAction'),
            style: BindingStyle::tryFrom($operation->getAttribute('style')) ?? BindingStyle::DOCUMENT,
        );
    }

    public function parseMessageImplementation(Document $wsdl, DOMElement $message): MessageImplementation
    {
        $body = locate_by_tag_name($message, 'body')->item(0);
        if (!$body) {
            return new SoapMessage(bindingUse: BindingUse::LITERAL);
        }

        return new SoapMessage(
            bindingUse: BindingUse::tryFrom($body->getAttribute('use')) ?? BindingUse::LITERAL,
        );
    }

    private function parseVersionFromNode(Document $wsdl, DOMElement $element): SoapVersion
    {
        return (new SoapVersionParser())($wsdl, $element);
    }
}
