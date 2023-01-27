<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Formatter;

use Soap\Engine\Metadata\Model\XsdType;

final class XsdTypeFormatter
{
    public function __invoke(XsdType $xsdType): string
    {
        $meta = $xsdType->getMeta();
        $min = (int)($meta['min'] ?? 1);
        $max = (int)($meta['max'] ?? 1);
        $nil = (bool)($meta['nil'] ?? false);

        $nullable = $nil || ($min === 0 && $max === 1);
        $list = ($max > 1 || $max === -1);

        return join('', [
            $nullable ? '?': '',
            $xsdType->getName(), //$xsdType->getXmlNamespace().':'.$xsdType->getName(),
            $list ? '[]' : ''
        ]);
    }
}
