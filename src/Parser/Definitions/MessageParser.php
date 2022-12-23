<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Message;
use Soap\WsdlReader\Model\Definitions\Messages;
use Soap\WsdlReader\Model\Definitions\Part;
use Soap\WsdlReader\Model\Definitions\Parts;
use Soap\WsdlReader\Model\Definitions\QNamed;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;

class MessageParser
{
    public function __invoke(Document $wsdl, DOMElement $message): Message
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new Message(
            name: $message->getAttribute('name'),
            parts: new Parts(
                ...$xpath->query('./wsdl:part', $message)
                    ->expectAllOfType(DOMElement::class)
                    ->map(
                        fn (DOMElement $part) => new Part(
                            name: $part->getAttribute('name'),
                            element: QNamed::parse($part->getAttribute('element') ?: $part->getAttribute('type'))
                        )
                    )
            )
        );
    }

    public static function tryParse(Document $wsdl): Messages
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        $parse = new self();

        return new Messages(
            ...$xpath->query('/wsdl:definitions/wsdl:message')
                ->expectAllOfType(DOMElement::class)
                ->map(
                    fn (DOMElement $message) => $parse($wsdl, $message)
                )
        );
    }
}
