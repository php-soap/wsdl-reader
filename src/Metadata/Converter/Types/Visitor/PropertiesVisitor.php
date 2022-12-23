<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\Attribute;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeContainer;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\Group;
use GoetasWebservices\XML\XSDReader\Schema\Element\Element;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementContainer;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

class PropertiesVisitor
{
    /**
     * @return \Generator<Property>
     */
    public function __invoke(Type $type, TypesConverterContext $context): \Generator
    {
        $elements = $type instanceof ElementContainer ? $type->getElements() : [];
        if ($elements) {
            /** @var Element $element */
            foreach ($elements as $element) {
                $name = $element->getType()->getName() ?: $element->getName();

                yield new Property(
                    $element->getName(),
                    (new XsdType($name))
                        ->withXmlNamespace($element->getSchema()->getTargetNamespace())
                        ->withXmlNamespaceName('TODO') // TODO
                        ->withMeta([
                            'min' => $element->getMin(),
                            'max' => $element->getMax(),
                            'default' => $element->getDefault(),
                            'docs' => $element->getDoc(),
                            // 'type' => $element->getType()
                        ])
                );
            }

            return;
        }

        $attributes = $type instanceof AttributeContainer ? $type->getAttributes() : [];
        if ($attributes) {
            // The content of the type:
            yield new Property('_', new XsdType('todo'));

            /** @var Attribute|Group $attribute */
            foreach ($attributes as $attribute) {
                if ($attribute instanceof Group) {
                    continue;
                }


                $attributeType = $attribute->getType();
                $name = $attributeType->getName() ?: $attribute->getName();

                yield new Property(
                    $attribute->getName(),
                    (new XsdType($name))
                        ->withXmlNamespace($attribute->getSchema()->getTargetNamespace())
                        ->withXmlNamespaceName('TODO') // TODO
                        ->withMeta([
                            'use' => $attribute->getUse(),
                            'docs' => $attribute->getDoc(),
                            'default' => $attribute->getDefault(),
                            'fixed' => $attribute->getFixed(),
                            'restrictions' => $attributeType->getRestriction() ? $attributeType->getRestriction()->getChecks() : [],
                        ])
                );
            }
            return;
        }
    }
}
