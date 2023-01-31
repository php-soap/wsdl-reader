<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class SimpleTypeConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof SimpleType) {
            return $metaType;
        }

        $configure = pipe(
            static fn (MetaType $metaType): MetaType => (new RestrictionsConfigurator())($metaType, $xsdType->getRestriction(), $context),
            static fn (MetaType $metaType): MetaType => (new SimpleListConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new SimpleUnionsConfigurator())($metaType, $xsdType, $context),
        );

        return $configure(
            $metaType
                ->withMeta([
                    ...$metaType->getMeta(),
                    'isSimple' => true,
                ])
        );
    }
}
