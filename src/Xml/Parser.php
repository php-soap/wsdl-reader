<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Xml;

use DOMDocument;
use Soap\WsdlReader\Loader\Loader;
use Soap\WsdlReader\Xml\Configurator\FlattenWsdlImports;
use Soap\WsdlReader\Xml\Configurator\FlattenXsdImports;
use VeeWee\Xml\Dom\Document;
use function Psl\Fun\pipe;
use function Psl\Fun\when;
use function VeeWee\Xml\Dom\Configurator\utf8;

class Parser
{
    public function __construct(
        private Loader $loader
    ) {
    }

    public function parse(string $location): Document
    {
        return Document::fromXmlString(
            $this->loader->load($location),
            pipe(
                utf8(),
                when(
                    static fn(DOMDocument $document): bool => $document->documentElement->localName === 'definitions',
                    pipe(
                        new FlattenWsdlImports($this, $location),
                        new FlattenXsdImports($this, $location)
                    ),
                    new FlattenXsdImports($this, $location)
                )
            )
        );
    }
}
