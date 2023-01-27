<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Strategy;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Implementation\Binding\BindingImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Message\MessageImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Operation\OperationImplementation;
use VeeWee\Xml\Dom\Document;

interface StrategyInterface
{
    public function parseBindingImplementation(Document $wsdl, DOMElement $binding): BindingImplementation;
    public function parseOperationImplementation(Document $wsdl, DOMElement $operation): OperationImplementation;
    public function parseMessageImplementation(Document $wsdl, DOMElement $message): MessageImplementation;
}
