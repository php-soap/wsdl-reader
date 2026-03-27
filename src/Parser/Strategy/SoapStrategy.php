<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Strategy;

use Dom\Element;
use Soap\WsdlReader\Model\Definitions\BindingStyle;
use Soap\WsdlReader\Model\Definitions\BindingUse;
use Soap\WsdlReader\Model\Definitions\EncodingStyle;
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
use VeeWee\Xml\Xmlns\Xmlns;
use function VeeWee\Xml\Dom\Locator\Element\locate_by_namespaced_tag_name;

final class SoapStrategy implements StrategyInterface
{
    public function parseBindingImplementation(Document $wsdl, Element $binding): BindingImplementation
    {
        return new SoapBinding(
            version: $this->parseVersionFromNode($wsdl, $binding),
            transport: TransportType::from($binding->getAttribute('transport') ?? ''),
            style: BindingStyle::tryFromCaseInsensitive($binding->getAttribute('style') ?? ''),
        );
    }

    public function parseOperationImplementation(Document $wsdl, Element $operation): OperationImplementation
    {
        return new SoapOperation(
            version: $this->parseVersionFromNode($wsdl, $operation),
            action: $operation->getAttribute('soapAction') ?? '',
            style: BindingStyle::tryFromCaseInsensitive($operation->getAttribute('style') ?? ''),
        );
    }

    public function parseMessageImplementation(Document $wsdl, Element $message): MessageImplementation
    {
        $body = locate_by_namespaced_tag_name($message, '*', 'body')->item(0);
        if (!$body) {
            return new SoapMessage(
                bindingUse: BindingUse::LITERAL,
                namespace: null,
                encodingStyle: null
            );
        }

        $namespace = $body->getAttribute('namespace');

        return new SoapMessage(
            bindingUse: BindingUse::tryFromCaseInsensitive($body->getAttribute('use') ?? '') ?? BindingUse::LITERAL,
            namespace: $namespace !== null ? Xmlns::load($namespace) : null,
            encodingStyle: EncodingStyle::tryFrom($body->getAttribute('encodingStyle') ?? ''),
        );
    }

    private function parseVersionFromNode(Document $wsdl, Element $element): SoapVersion
    {
        return (new SoapVersionParser())($wsdl, $element);
    }
}
