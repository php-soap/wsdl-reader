<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class ElementConfigurator
{
    public function __invoke(MetaType $metaType, mixed $element, TypesConverterContext $context): MetaType
    {
        if (!$element instanceof ElementSingle) {
            return $metaType;
        }

        $xsdType = $element->getType();

        return pipe(
            static fn (MetaType $metaType): MetaType => (new TypeConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new NamespaceConfigurator())($metaType, $element, $context),
            static fn (MetaType $metaType): MetaType => (new DocsConfigurator())($metaType, $element, $context),
            static fn (MetaType $metaType): MetaType => (new DefaultConfigurator())($metaType, $element, $context),
            static fn (MetaType $metaType): MetaType => (new FixedConfigurator())($metaType, $element, $context),
            static fn (MetaType $metaType): MetaType => (new OccurrencesConfigurator())($metaType, $element, $context),
            static fn (MetaType $metaType): MetaType => (new ElementSingleConfigurator())($metaType, $element, $context),
        )($metaType);
    }
}
