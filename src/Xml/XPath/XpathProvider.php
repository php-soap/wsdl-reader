<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Xml\Xpath;

use Soap\WsdlReader\Xml\Xmlns;
use VeeWee\Xml\Dom\Document;
use VeeWee\Xml\Dom\Xpath;
use function Psl\Dict\merge;
use function VeeWee\Xml\Dom\Locator\document_element;

final class XpathProvider
{
    public static function provide(Document $document): Xpath
    {
        $tns = $document->map(document_element())->getAttribute('targetNamespace');

        return $document->xpath(Xpath\Configurator\namespaces(
            merge(
                [
                    'wsdl' => Xmlns::wsdl()->value(),
                    'soap' => Xmlns::soap()->value(),
                    'schema' => Xmlns::xsd()->value(),
                ],
                $tns ? ['tns' => $tns] : []
            )
        ));
    }
}
