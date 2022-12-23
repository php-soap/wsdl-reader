<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementDef;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Model\Type as SoapType;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

class ElementVisitor
{
    public function __invoke(ElementDef $element, TypesConverterContext $context): ?SoapType
    {
        // When there is no type set on the element, we cannot do anything with it here.
        $type = $element->getType();
        if (!$type) {
            // TODO : could be optional / nil / ... ?

            return null;
        }

        // On <element name="Categories" type="tns:Categories" />
        // The element proxies to a type.
        // This method will skip the Element -> The complex type will be imported instead!
        if ($type->getName() === $element->getName() && $type->getSchema()->getTargetNamespace() === $element->getSchema()->getTargetNamespace()) {
            return null;
        }

        $restrictions = $type->getRestriction();
        $parent = $type->getParent();
        $extension = $type->getExtension();

        return new SoapType(
            (new XsdType($element->getName()))
                ->withMeta([
                    'docs' => $element->getDoc() ?: $type->getDoc(),
                    'abstract' => $type->isAbstract(),
                    'restrictions' => $restrictions ? $restrictions->getChecks() : [],
                    'parent' => $parent && method_exists($parent, 'getChecks') ? $parent->getChecks() : [],
                    'extension' => $extension?->getBase()?->getName() ?? '',
                ])
                ->withXmlNamespaceName('TODO') // TODO
                ->withXmlNamespace($element->getSchema()->getTargetNamespace()),
            new PropertyCollection(...(new PropertiesVisitor())($element->getType(), $context))
        );
    }
}

