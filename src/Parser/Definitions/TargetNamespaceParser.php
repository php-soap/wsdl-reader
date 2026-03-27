<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use VeeWee\Xml\Dom\Document;
use VeeWee\Xml\Xmlns\Xmlns;

final class TargetNamespaceParser
{
    public static function tryParse(Document $wsdl): ?Xmlns
    {
        $definitions = $wsdl->locateDocumentElement();

        $targetNamespace = $definitions->getAttribute('targetNamespace');

        return $targetNamespace !== null
            ? Xmlns::load($targetNamespace)
            : null;
    }
}
