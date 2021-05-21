<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Reader\Iterator;

use Exception;
use Soap\WsdlReader\Xml\Xpath\XpathProvider;
use Traversable;
use VeeWee\Xml\Dom\Document;
use Psl\Type;

class PortIterator implements \IteratorAggregate
{
    private Document $wsdl;

    public function __construct(Document $wsdl)
    {
        $this->wsdl = $wsdl;
    }

    public function getIterator(): \Generator
    {
        $xpath = XpathProvider::provide($this->wsdl);

        yield from array_reduce(
            [...$xpath->query('/wsdl:definitions/wsdl:portType')],
            fn (array $ports, \DOMElement $port): array => array_merge(
                $ports,
                [
                    $port->getAttribute('name') => [
                        'name' => $port->getAttribute('name'),
                        'operations' => array_reduce(
                            [...$xpath->query('./wsdl:operation', $port)],
                            fn(array $bindings, \DOMElement $operation) => array_merge(
                                $bindings,
                                [
                                    $operation->getAttribute('name') => [
                                        'name' => $operation->getAttribute('name'),
                                        'input' => [
                                            'name' => $xpath->evaluate('string(./wsdl:input/@name)', Type\string(), $operation),
                                            'message' => $xpath->evaluate('string(./wsdl:input/@message)',Type\string(), $operation),
                                        ],
                                        'output' => [
                                            'name' => $xpath->evaluate('string(./wsdl:output/@name)',Type\string(), $operation),
                                            'message' => $xpath->evaluate('string(./wsdl:output/@message)',Type\string(), $operation),
                                        ]
                                    ],
                                ]
                            ),
                            []
                        )
                    ],
                ]
            ),
            []
        );
    }
}
