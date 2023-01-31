<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Mapper;

use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\map;

final class UnionTypesMapper
{
    /**
     * @template T
     * @param array<array-key, SimpleType> $types
     * @param callable(SimpleType, array<string, mixed>): T $mapper
     * @return list<T>
     */
    public function __invoke(array $types, callable $mapper): array
    {
        return filter_nulls(
            map(
                $types,
                /**
                 * @return T|null
                 */
                static function (SimpleType $union) use ($mapper): mixed {
                    if ($union->getName()) {
                        return $mapper($union, ['isList' => false]);
                    }

                    if ($list = $union->getList()) {
                        return $mapper($list, ['isList' => true]);
                    }

                    $base = $union->getParent()?->getBase();
                    if ($base instanceof SimpleType) {
                        return $mapper($base, ['isList' => false]);
                    }

                    return null;
                }
            )
        );
    }
}
