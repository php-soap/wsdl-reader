<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Formatter;

use Soap\Engine\Metadata\Model\XsdType;
use function Psl\Vec\map;

final class UnionFormatter
{
    public function __invoke(XsdType $type): string
    {
        $meta = $type->getMeta();
        $isList = $meta->isList()->unwrapOr(false);
        $unions = $meta->unions()->unwrapOr([]);
        if (!$unions) {
            return '';
        }

        return ' = ('.($isList ? 'list<' : '').join('|', map(
            $unions,
            static function (array $union): string {
                $isList = $union['isList'];
                return $isList ? 'list<'.$union['type'].'>' : $union['type'];
            }
        )).($isList ? '>' : '').')';
    }
}
