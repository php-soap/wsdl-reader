<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Element\AbstractElementSingle;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementItem;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementRef;
use GoetasWebservices\XML\XSDReader\Schema\Type\ComplexType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type as EngineType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\Configurator;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\map;

final class TypeVisitor
{
    public function __invoke(Type $xsdType, TypesConverterContext $context): TypeCollection
    {
        $configure = pipe(
            static fn (MetaType $metaType): MetaType => (new Configurator\TypeConfigurator())($metaType, $xsdType, $context),
        );

        return new TypeCollection(
            new EngineType(
                $configure(MetaType::guess($xsdType->getName() ?? '')),
                (new PropertiesVisitor())($xsdType, $context)
            ),
            ...$this->parseNestedInlineElements($xsdType, $context),
        );
    }

    /**
     * Complex types may contain nested inline types.
     * Let's unwrap them:
     */
    private function parseNestedInlineElements(Type $xsdType, TypesConverterContext $context): TypeCollection
    {
        if (!$xsdType instanceof ComplexType) {
            return new TypeCollection();
        }

        $elementVisitor = new ElementVisitor();

        return new TypeCollection(
            ...filter_nulls(
                map(
                    $xsdType->getElements(),
                    static function (ElementItem $element) use ($elementVisitor, $context): ?EngineType {
                        if (!$element instanceof AbstractElementSingle || $element instanceof ElementRef) {
                            return null;
                        }

                        // There is no need to create types for simple elements like strings.
                        if (!$element->getType() instanceof ComplexType) {
                            return null;
                        }

                        // If the element links to a named type, we already know about it.
                        if ($element->getType()?->getName()) {
                            return null;
                        }

                        return $elementVisitor($element, $context);
                    }
                )
            )
        );
    }
}
