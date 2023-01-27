<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\SchemaItem;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class DocsConfigurator
{
    public function __invoke(MetaType $metaType, SchemaItem $xsdType, TypesConverterContext $context): MetaType
    {
        return $metaType
            ->withMeta([
                ...$metaType->getMeta(),
                'docs' => $xsdType->getDoc() ?: ($metaType->getMeta()['docs'] ?? ''),
            ]);
    }
}
