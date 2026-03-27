<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Strategy;

use Dom\Element;
use Soap\WsdlReader\Model\Definitions\Implementation\Binding\BindingImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Message\MessageImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Operation\OperationImplementation;
use VeeWee\Xml\Dom\Document;

interface StrategyInterface
{
    public function parseBindingImplementation(Document $wsdl, Element $binding): BindingImplementation;
    public function parseOperationImplementation(Document $wsdl, Element $operation): OperationImplementation;
    public function parseMessageImplementation(Document $wsdl, Element $message): MessageImplementation;
}
