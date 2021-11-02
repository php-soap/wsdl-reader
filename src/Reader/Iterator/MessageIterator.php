<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Reader\Iterator;

use Exception;
use Soap\Xml\Xpath\WsdlPreset;
use Traversable;
use VeeWee\Xml\Dom\Document;
use Psl\Type;
use VeeWee\Xml\Dom\Xpath;

class MessageIterator implements \IteratorAggregate
{
    private Document $wsdl;

    public function __construct(Document $wsdl)
    {
        $this->wsdl = $wsdl;
    }

    public function getIterator(): \Generator
    {
        $xpath = Xpath::fromDocument($this->wsdl, new WsdlPreset($this->wsdl));

        yield from array_reduce(
            [...$xpath->query('/wsdl:definitions/wsdl:message')],
            fn (array $messages, \DOMElement $message): array => array_merge(
                $messages,
                [
                    $message->getAttribute('name') => [
                        'name' => $message->getAttribute('name'),
                        'parts' => array_reduce(
                            [...$xpath->query('./wsdl:part', $message)],
                            fn(array $parts, \DOMElement $part) => array_merge(
                                $parts,
                                [
                                    $part->getAttribute('name') => [
                                        'name' => $part->getAttribute('name'),
                                        'element' => $part->getAttribute('element')
                                    ]
                                ]
                            ),
                            []
                        )
                    ]
                ]
            ),
            []
        );
    }
}
