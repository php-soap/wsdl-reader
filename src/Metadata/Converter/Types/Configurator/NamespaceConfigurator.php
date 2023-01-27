<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\SchemaItem;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class NamespaceConfigurator
{
    public function __invoke(MetaType $metaType, SchemaItem $xsdType, TypesConverterContext $context): MetaType
    {
        $currentNamespace = $xsdType->getSchema()->getTargetNamespace();

        return $metaType
            ->withXmlNamespace($currentNamespace)
            ->withXmlNamespaceName(
                $context->knownNamespaces->lookupNameFromNamespace($currentNamespace)->unwrapOr(
                    $metaType->getXmlNamespaceName()
                )
            );
    }
}
