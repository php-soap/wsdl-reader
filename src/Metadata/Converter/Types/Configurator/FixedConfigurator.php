<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Element\InterfaceSetFixed;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class FixedConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof InterfaceSetFixed) {
            return $metaType;
        }

        return $metaType
            ->withMeta([
                ...$metaType->getMeta(),
                'fixed' => $xsdType->getFixed(),
            ]);
    }
}
