<?php declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Element\Any\Any;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use Soap\Xml\Xmlns;
use function Psl\Fun\pipe;

final class AnyElementConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof Any) {
            return $engineType;
        }

        $xsd = Xmlns::xsd()->value();
        $targetNamespace = $xsdType->getSchema()->getTargetNamespace() ?? '';

        $configure = pipe(
            static fn (MetaType $metaType): MetaType => (new DocsConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new OccurrencesConfigurator())($metaType, $xsdType, $context),
        );

        return $configure(
            $engineType
                ->withXmlTargetNodeName('any')
                ->withXmlTypeName('any')
                ->withBaseType('anyXML')
                ->withXmlNamespace($xsd)
                ->withXmlNamespaceName($context->knownNamespaces->lookupNameFromNamespace($xsd)->unwrapOr('xsd'))
                ->withXmlTargetNamespace($targetNamespace)
                ->withXmlTargetNamespaceName(
                    $context->knownNamespaces->lookupNameFromNamespace($targetNamespace)->unwrapOr(
                        $engineType->getXmlNamespaceName()
                    )
                )
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta
                    ->withRestriction([
                        'processXMLContent' => [
                            ['value' => $xsdType->getProcessContents()->value],
                        ]
                    ])
                    ->withIsElement(true)
                )
        );
    }
}
