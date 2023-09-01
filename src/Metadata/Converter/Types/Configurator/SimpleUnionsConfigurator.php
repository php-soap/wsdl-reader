<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\Mapper\UnionTypesMapper;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class SimpleUnionsConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof SimpleType) {
            return $metaType;
        }

        if (!$unions = $xsdType->getUnions()) {
            return $metaType;
        }

        $mapUnions = new UnionTypesMapper();

        return $metaType
            ->withBaseType('mixed')
            ->withMemberTypes(
                $mapUnions(
                    $unions,
                    static fn (SimpleType $union): string => $union->getName() ?? ''
                )
            )
            ->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta
                    ->withIsAlias(true)
                    ->withUnions(
                        $mapUnions(
                            $unions,
                            static fn (SimpleType $union, array $meta) => [
                                ...$meta,
                                'type' => $union->getName(),
                                'namespace' => $union->getSchema()->getTargetNamespace(),
                            ]
                        )
                    )
            );
    }
}
