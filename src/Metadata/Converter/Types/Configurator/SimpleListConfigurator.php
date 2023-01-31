<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\Mapper\UnionTypesMapper;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Vec\filter_nulls;

final class SimpleListConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof SimpleType) {
            return $metaType;
        }

        if (!$list = $xsdType->getList()) {
            return $metaType;
        }

        $mapUnions = new UnionTypesMapper();
        $base = $list->getName() ? $list : $list->getParent()?->getBase();
        $unions = $list->getUnions();

        return $metaType
            ->withBaseType('array')
            ->withMemberTypes(filter_nulls([
                $base?->getName(),
                ...$mapUnions($unions, static fn (SimpleType $union): string => $union->getName())
            ]))
            ->withMeta([
                ...$metaType->getMeta(),
                'isList' => true,
                'isAlias' => true,
                'unions' => filter_nulls([
                    $base?->getName() ? [
                        'type' => $base?->getName(),
                        'namespace' => $base?->getSchema()->getTargetNamespace(),
                        'isList' => false,
                    ] : null,
                    ...$mapUnions(
                        $unions,
                        static fn (SimpleType $union, array $meta) => [
                            ...$meta,
                            'type' => $union->getName(),
                            'namespace' => $union->getSchema()->getTargetNamespace(),
                        ]
                    )
                ]),
            ]);
    }
}
