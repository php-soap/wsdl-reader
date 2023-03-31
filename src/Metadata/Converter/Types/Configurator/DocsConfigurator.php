<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\SchemaItem;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class DocsConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof SchemaItem) {
            return $engineType;
        }

        return $engineType
            ->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withDocs(
                    $xsdType->getDoc() ?: $meta->docs()->unwrapOr('')
                )
            );
    }
}
