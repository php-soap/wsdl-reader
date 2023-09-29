<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Formatter;

use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Predicate\IsConsideredNullableType;

final class XsdTypeFormatter
{
    public function __invoke(XsdType $xsdType): string
    {
        $meta = $xsdType->getMeta();
        $isList = $meta->isList()->unwrapOr(false) && !$meta->isAlias()->unwrapOr(false);
        $isNullable = (new IsConsideredNullableType())($meta);
        $min = $meta->minOccurs()->unwrapOr(1);
        $max = $meta->maxOccurs()->unwrapOr(1);

        return join('', [
            $isNullable ? '?': '',
            $isList ? 'array<int<'.($min === -1 ? 'min' : $min).', '.($max === -1 ? 'max' : $max).'>, ' : '',
            $xsdType->getName(), //$xsdType->getXmlNamespace().':'.$xsdType->getName(),
            $isList ? '>' : ''
        ]);
    }
}
