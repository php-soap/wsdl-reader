<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeItem;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementItem;
use GoetasWebservices\XML\XSDReader\Schema\Item;
use GoetasWebservices\XML\XSDReader\Schema\SchemaItem;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\Detector\AttributeTypeNameDetector;
use Soap\WsdlReader\Metadata\Converter\Types\Detector\ElementTypeNameDetector;
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
        $typeNamespace = match (true) {
            $item instanceof ElementItem => $targetNamespace,
            default => $type?->getSchema()->getTargetNamespace() ?: $targetNamespace,
        };

        $parentContext = $context->parent()->unwrapOr(null);
        $xmlTypeName = match(true) {
            $parentContext && $item instanceof ElementItem => (new ElementTypeNameDetector())($item, $parentContext),
            $parentContext && $item instanceof AttributeItem => (new AttributeTypeNameDetector())($item, $parentContext),
            default => $typeName,
        };

        return $engineType
            ->withXmlTargetNodeName($itemName ?: $typeName)
            ->withXmlTypeName($xmlTypeName)
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
