<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Item;
use GoetasWebservices\XML\XSDReader\Schema\SchemaItem;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class XmlTypeInfoConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof SchemaItem) {
            return $engineType;
        }

        $item = $xsdType instanceof Item ? $xsdType : null;
        $type = $xsdType instanceof Type ? $xsdType : $item?->getType();

        $itemName = $item?->getName() ?: $type?->getName();
        $typeName = $type?->getName() ?? '';
        $targetNamespace = $xsdType->getSchema()->getTargetNamespace() ?? '';
        $typeNamespace = $type?->getSchema()->getTargetNamespace() ?: $targetNamespace;

        return $engineType
            ->withXmlTargetNodeName($itemName ?: $typeName)
            ->withXmlTypeName($typeName ?: $itemName ?: '')
            ->withXmlNamespace($typeNamespace)
            ->withXmlNamespaceName(
                $context->knownNamespaces->lookupNameFromNamespace($typeNamespace)->unwrapOr(
                    $engineType->getXmlNamespaceName()
                )
            )
            ->withXmlTargetNamespace($targetNamespace)
            ->withXmlTargetNamespaceName(
                $context->knownNamespaces->lookupNameFromNamespace($targetNamespace)->unwrapOr(
                    $engineType->getXmlNamespaceName()
                )
            );
    }
}
