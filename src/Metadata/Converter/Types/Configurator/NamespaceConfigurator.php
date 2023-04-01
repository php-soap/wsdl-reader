<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\SchemaItem;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class NamespaceConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof SchemaItem) {
            return $engineType;
        }

        $currentNamespace = $xsdType->getSchema()->getTargetNamespace() ?? '';

        return $engineType
            ->withXmlNamespace($currentNamespace)
            ->withXmlNamespaceName(
                $context->knownNamespaces->lookupNameFromNamespace($currentNamespace)->unwrapOr(
                    $engineType->getXmlNamespaceName()
                )
            );
    }
}
