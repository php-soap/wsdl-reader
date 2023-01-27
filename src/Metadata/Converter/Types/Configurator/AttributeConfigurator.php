<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeItem;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class AttributeConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof AttributeItem) {
            return $metaType;
        }

        return pipe(
            static fn (MetaType $metaType): MetaType => (new NamespaceConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new DocsConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new AttributeSingleConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new AbstractAttributeItemConfigurator())($metaType, $xsdType, $context),
        )(
            $metaType
                ->withMeta([
                    ...$metaType->getMeta(),
                    'isAttribute' => true,
                ])
        );
    }
}
