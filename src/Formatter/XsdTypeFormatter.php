<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Formatter;

use Soap\Engine\Metadata\Model\XsdType;

final class XsdTypeFormatter
{
    public function __invoke(XsdType $xsdType): string
    {
        $meta = $xsdType->getMeta();
        $isList = ($meta['isList'] ?? false) && !($meta['isAlias'] ?? false);
        $isNullable = (bool)($meta['isNullable'] ?? false);
        $min = (int)($meta['min'] ?? 1);
        $max = (int)($meta['max'] ?? 1);

        return join('', [
            $isNullable ? '?': '',
            $isList ? 'array<int<'.($min === -1 ? 'min' : $min).', '.($max === -1 ? 'max' : $max).'>, ' : '',
            $xsdType->getName(), //$xsdType->getXmlNamespace().':'.$xsdType->getName(),
            $isList ? '>' : ''
        ]);
    }
}
