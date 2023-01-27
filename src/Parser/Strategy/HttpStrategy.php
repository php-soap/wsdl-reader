<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Strategy;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Implementation\Binding\BindingImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Binding\HttpBinding;
use Soap\WsdlReader\Model\Definitions\Implementation\Message\HttpMessage;
use Soap\WsdlReader\Model\Definitions\Implementation\Message\MessageImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Operation\HttpOperation;
use Soap\WsdlReader\Model\Definitions\Implementation\Operation\OperationImplementation;
use VeeWee\Xml\Dom\Document;

final class HttpStrategy implements StrategyInterface
{
    public function parseBindingImplementation(Document $wsdl, DOMElement $binding): BindingImplementation
    {
        return new HttpBinding(
            verb: $binding->getAttribute('verb')
        );
    }

    public function parseOperationImplementation(Document $wsdl, DOMElement $operation): OperationImplementation
    {
        return new HttpOperation(
            location: $operation->getAttribute('location')
        );
    }

    public function parseMessageImplementation(Document $wsdl, DOMElement $message): MessageImplementation
    {
        return new HttpMessage();
    }
}
