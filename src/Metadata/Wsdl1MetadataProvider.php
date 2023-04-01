<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata;

use Soap\Engine\Metadata\InMemoryMetadata;
use Soap\Engine\Metadata\LazyMetadataProvider;
use Soap\Engine\Metadata\Metadata;
use Soap\Engine\Metadata\MetadataProvider;
use Soap\WsdlReader\Locator\ServiceSelectionCriteria;
use Soap\WsdlReader\Metadata\Converter\Methods\MethodsConverterContext;
use Soap\WsdlReader\Metadata\Converter\SchemaToTypesConverter;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use Soap\WsdlReader\Metadata\Converter\Wsdl1ToMethodsConverter;
use Soap\WsdlReader\Model\Wsdl1;

final class Wsdl1MetadataProvider implements MetadataProvider
{
    private MetadataProvider $provider;

    public function __construct(
        public readonly Wsdl1 $wsdl,
        public readonly ?ServiceSelectionCriteria $serviceSelectionCriteria = null
    ) {
        $this->provider = new LazyMetadataProvider(
            static function () use ($wsdl, $serviceSelectionCriteria): Metadata {
                return new InMemoryMetadata(
                    $types = (new SchemaToTypesConverter())(
                        $wsdl->schema,
                        TypesConverterContext::default($wsdl->namespaces)
                    ),
                    $methods = (new Wsdl1ToMethodsConverter())(
                        $wsdl,
                        MethodsConverterContext::defaults($types, $serviceSelectionCriteria)
                    )
                );
            }
        );
    }

    public function getMetadata(): Metadata
    {
        return $this->provider->getMetadata();
    }
}
