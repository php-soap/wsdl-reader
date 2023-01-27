<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Element\InterfaceSetDefault;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class DefaultConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof InterfaceSetDefault) {
            return $metaType;
        }

        return $metaType
            ->withMeta([
                ...$metaType->getMeta(),
                'default' => $xsdType->getDefault(),
            ]);
    }
}
