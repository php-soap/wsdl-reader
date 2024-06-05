<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator\SoapEnc;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle;
use GoetasWebservices\XML\XSDReader\Schema\Type\ComplexType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type as XsdType;
use Psl\Option\Option;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\Detector\AttributesCustomAttributeDetector;
use Soap\WsdlReader\Metadata\Converter\Types\Detector\NamedAttributesDetector;
use Soap\WsdlReader\Metadata\Converter\Types\SoapEnc\ArrayTypeInfo;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use Soap\WsdlReader\Metadata\Detector\Soap11ArrayDetector;
use Soap\Xml\Xmlns;
use function Psl\Option\none;
use function Psl\Option\some;
use function Psl\Vec\filter_nulls;

final class Soap11ArrayConfigurator
{
    public function __invoke(MetaType $metaType, XsdType $xsdType, TypesConverterContext $context): MetaType
    {
        $base = $xsdType->getRestriction()?->getBase();
        if (!$xsdType instanceof ComplexType || !$base instanceof ComplexType) {
            return $metaType;
        }

        $namespace = $base->getSchema()->getTargetNamespace() ?? '';
        $typeName = $base->getName() ?? '';
        if (!Soap11ArrayDetector::detect($namespace, $typeName)) {
            return $metaType;
        }

        return $this->parseFromElement($metaType, $xsdType, $context)
            ->or($this->parseFromAttribute($metaType, $xsdType, $context))
            ->unwrapOr($metaType);
    }

    /**
     * @param ComplexType $xsdType
     * @return Option<MetaType>
     */
    private function parseFromElement(MetaType $metaType, XsdType $xsdType, TypesConverterContext $context): Option
    {
        if (!$xsdType->getElements()) {
            return none();
        }

        $element = $xsdType->getElements()[0];
        if (!$element instanceof ElementSingle) {
            return none();
        }

        $type = $element->getType();
        $typeName = $type?->getName();
        if (!$type || !$typeName) {
            return none();
        }

        $namespace = $type->getSchema()->getTargetNamespace() ?? '';
        $arrayTypeInfo = new ArrayTypeInfo(
            $context->knownNamespaces->lookupNameFromNamespace($namespace)->unwrap(),
            $typeName,
            '['.($element->getMax() > -1 ? (string) $element->getMax() : '').']'
        );

        return some(
            $this->applyArrayTypInfoToMeta($metaType, $arrayTypeInfo, $namespace, $element->getName())
        );
    }

    /**
     * @return Option<MetaType>
     */
    private function parseFromAttribute(MetaType $metaType, XsdType $xsdType, TypesConverterContext $context): Option
    {
        $attrs = (new NamedAttributesDetector())($xsdType);
        $arrayTypeResult = (new AttributesCustomAttributeDetector())($attrs, 'arrayType', 'arrayType');

        if ($arrayTypeResult->isNone()) {
            return none();
        }

        $arrayType = $arrayTypeResult->unwrap();
        $arrayTypeInfo = ArrayTypeInfo::parseSoap11($arrayType->getValue());
        if (!$arrayTypeInfo->prefix) {
            $arrayTypeInfo = $arrayTypeInfo->withPrefix(
                $context->knownNamespaces->lookupNameFromNamespace(Xmlns::xsd()->value())->unwrap()
            );
        }
        $namespace = $context->knownNamespaces->lookupNamespaceFromName($arrayTypeInfo->prefix)->unwrap();

        return some($this->applyArrayTypInfoToMeta($metaType, $arrayTypeInfo, $namespace));
    }

    private function applyArrayTypInfoToMeta(
        MetaType $metaType,
        ArrayTypeInfo $arrayTypeInfo,
        string $namespace,
        ?string $arrayNodeName = null
    ): MetaType {
        return $metaType
            ->withBaseType('array')
            ->withMemberTypes([$arrayTypeInfo->type])
            ->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta
                    ->withIsElement(true)
                    ->withIsSimple(false)
                    ->withIsList(true)
                    ->withIsAlias(true)
                    ->withMinOccurs(0)
                    ->withMaxOccurs($arrayTypeInfo->getMaxOccurs())
                    ->withUnions(
                        filter_nulls([
                            [
                                'type' => $arrayTypeInfo->type,
                                'namespace' => $namespace,
                                'isList' => false,
                            ]
                        ])
                    )
                    ->withArrayType([
                        'type' => $arrayTypeInfo->toString(),
                        'itemType' => $arrayTypeInfo->itemType(),
                        'namespace' => $namespace,
                    ])
                    ->withArrayNodeName($arrayNodeName)
            );
    }
}
