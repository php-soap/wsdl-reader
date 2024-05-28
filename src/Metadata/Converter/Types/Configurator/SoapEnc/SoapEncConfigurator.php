<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator\SoapEnc;

use GoetasWebservices\XML\XSDReader\Schema\Type\Type as XsdType;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class SoapEncConfigurator
{
    public function __invoke(MetaType $metaType, XsdType $xsdType, TypesConverterContext $context): MetaType
    {
        return pipe(
            static fn (MetaType $metaType): EngineType => (new Soap11ArrayConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): EngineType => (new Soap12ArrayConfigurator())($metaType, $xsdType, $context),
        )($metaType);
    }
}
