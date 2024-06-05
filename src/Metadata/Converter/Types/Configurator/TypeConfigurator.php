<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\Type as XsdType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\Configurator\SoapEnc\SoapEncConfigurator;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class TypeConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof XsdType) {
            return $metaType;
        }

        return pipe(
            static fn (MetaType $metaType): MetaType => (new XmlTypeInfoConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new DocsConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new RestrictionsConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new AbstractConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new ExtendsConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new SimpleTypeConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new SoapEncConfigurator())($metaType, $xsdType, $context),
        )($metaType);
    }
}
