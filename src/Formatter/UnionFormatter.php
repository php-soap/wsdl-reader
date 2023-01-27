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
        $isList = (bool)($meta['isList'] ?? false);
        $unions = (array)($meta['unions'] ?? []);
        if (!$unions) {
            return '';
        }

        return ' = ('.($isList ? 'list<' : '').join('|', map(
            $unions,
            static function (array $union): string {
                $isList = (bool)($union['isList'] ?? false);
                return $isList ? 'list<'.$union['type'].'>' : $union['type'];
            }
        )).($isList ? '>' : '').')';
    }
}
