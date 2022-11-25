<?php

declare(strict_types=1);

namespace Soap\WsdlReader\OldStuff\Reader\Iterator;

use DOMElement;
use Generator;
use IteratorAggregate;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use VeeWee\Xml\Dom\Xpath;

final class MessageIterator implements IteratorAggregate
{
    private Document $wsdl;

    public function __construct(Document $wsdl)
    {
        $this->wsdl = $wsdl;
    }

    public function getIterator(): Generator
    {
        $xpath = Xpath::fromDocument($this->wsdl, new WsdlPreset($this->wsdl));

        yield from array_reduce(
            [...$xpath->query('/wsdl:definitions/wsdl:message')],
            static fn (array $messages, DOMElement $message): array => array_merge(
                $messages,
                [
                    $message->getAttribute('name') => [
                        'name' => $message->getAttribute('name'),
                        'parts' => array_reduce(
                            [...$xpath->query('./wsdl:part', $message)],
                            static fn (array $parts, DOMElement $part) => array_merge(
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
