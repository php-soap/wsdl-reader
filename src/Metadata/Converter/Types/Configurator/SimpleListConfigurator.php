<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\Mapper\UnionTypesMapper;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Vec\filter_nulls;

final class SimpleListConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof SimpleType) {
            return $engineType;
        }

        if (!$list = $xsdType->getList()) {
            return $engineType;
        }

        $mapUnions = new UnionTypesMapper();
        $base = $list->getName() ? $list : $list->getParent()?->getBase();
        $unions = $list->getUnions();

        return $engineType
            ->withBaseType('array')
            ->withMemberTypes(filter_nulls([
                $base?->getName(),
                ...$mapUnions($unions, static fn (SimpleType $union): string => $union->getName() ?? '')
            ]))
            ->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta
                    ->withIsList(true)
                    ->withIsAlias(true)
                    ->withUnions(
                        filter_nulls([
                            $base?->getName() ? [
                                'type' => $base->getName(),
                                'namespace' => $base->getSchema()->getTargetNamespace(),
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
                        ])
                    )
            );
    }
}
