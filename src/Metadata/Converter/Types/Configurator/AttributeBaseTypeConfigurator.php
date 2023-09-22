<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AbstractAttributeItem;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeItem;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeRef;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class AttributeBaseTypeConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof AttributeItem) {
            return $engineType;
        }

        $baseType = match (true) {
            $xsdType instanceof AbstractAttributeItem => $xsdType->getType(),
            $xsdType instanceof AttributeRef => $xsdType->getReferencedAttribute()->getType(),
            default => null,
        };

        return (new SimpleTypeConfigurator())($engineType, $baseType, $context);
    }
}
