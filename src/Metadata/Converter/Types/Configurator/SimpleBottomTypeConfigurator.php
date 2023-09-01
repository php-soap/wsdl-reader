<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class SimpleBottomTypeConfigurator
{
    public function __invoke(EngineType $engineType, ?Type $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType || !$xsdType instanceof SimpleType || $context->isBaseSchema($xsdType->getSchema())) {
            return $engineType->withBaseType(
                $engineType->getBaseType() ?: 'mixed'
            );
        }

        do {
            if ($result = $this->detectBaseType($engineType, $xsdType, $context)) {
                return $result;
            }
        } while ($xsdType = $xsdType->getParent()?->getBase());

        return $engineType;
    }

    private function detectBaseType(EngineType $engineType, ?Type $xsdType, TypesConverterContext $context): ?EngineType
    {
        if (!$xsdType) {
            return $engineType->withBaseType(
                $engineType->getBaseType() ?: 'mixed'
            );
        }

        if ($context->isBaseSchema($xsdType->getSchema())) {
            return $engineType->withBaseType(
                $xsdType->getName() ?: $engineType->getBaseType()
            );
        }

        if ($xsdType instanceof SimpleType) {
            if ($xsdType->getList()) {
                return (new SimpleListConfigurator())($engineType, $xsdType, $context);
            }

            if ($xsdType->getUnions()) {
                return (new SimpleUnionsConfigurator())($engineType, $xsdType, $context);
            }
        }

        return null;
    }
}
