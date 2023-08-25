<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Mapper;

use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class BaseTypeMapper
{
    public function __invoke(EngineType $engineType, ?Type $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType || $context->isBaseSchema($xsdType->getSchema())) {
            return $engineType;
        }

        do {
            if ($result = $this->detectBaseType($engineType, $xsdType, $context)) {
                return $result;
            }
        } while ($xsdType = $xsdType->getParent()?->getBase());

        return $engineType;
    }

    private function detectBaseType(EngineType $engineType, ?Type $xsdType, TypesConverterContext $contex): ?EngineType
    {
        if (!$xsdType) {
            return $engineType;
        }

        if ($contex->isBaseSchema($xsdType->getSchema())) {
            return $engineType->withBaseType(
                $xsdType->getName() ?: $engineType->getBaseType()
            );
        }

        if ($xsdType instanceof SimpleType) {
            if ($xsdType->getList()) {
                return $engineType
                    ->withBaseType('array')
                    ->withMeta(
                        static fn (TypeMeta $meta): TypeMeta => $meta->withIsList(true)
                    );
            }

            if ($xsdType->getUnions()) {
                return $engineType
                    ->withBaseType('mixed');
            }
        }

        return null;
    }
}
