<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\Type as XsdType;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class AbstractConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof XsdType) {
            return $engineType;
        }

        return $engineType
            ->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsAbstract($xsdType->isAbstract())
            );
    }
}
