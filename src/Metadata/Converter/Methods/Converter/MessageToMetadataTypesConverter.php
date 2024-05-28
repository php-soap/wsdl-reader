<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Converter;

use Soap\Engine\Exception\MetadataException;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type as EngineType;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Detector;
use Soap\WsdlReader\Model\Definitions\Message;
use Soap\WsdlReader\Model\Definitions\Namespaces;
use Soap\WsdlReader\Model\Definitions\Part;
use Soap\WsdlReader\Model\Definitions\QNamed;
use Soap\Xml\Xmlns;
use function Psl\Dict\pull;

final class MessageToMetadataTypesConverter
{
    public function __construct(
        private TypeCollection $knownTypes,
        private Namespaces $namespaces
    ) {
    }

    /**
     * @return array<string, XsdType>
     */
    public function __invoke(Message $message): array
    {
        return pull(
            $message->parts->items,
            fn (Part $part): XsdType => $this->findXsdType($part->element)
                ->withXmlTargetNodeName($part->name)
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta
                        ->withIsElement(true)
                ),
            static fn (Part $message): string => $message->name
        );
    }

    private function findXsdType(?QNamed $type): XsdType
    {
        if ($type === null) {
            return $this->createAnyType();
        }

        $namespace = $this->namespaces->lookupNamespaceByQname($type);

        try {
            return $namespace->mapOrElse(
                fn (string $namespace): EngineType => $this->knownTypes->fetchByNameAndXmlNamespace($type->localName, $namespace),
                fn (): EngineType => $this->knownTypes->fetchFirstByName($type->localName),
            )->unwrap()->getXsdType();
        } catch (MetadataException $e) {
            return $this->createSimpleTypeByQNamed($type);
        }
    }

    private function createSimpleTypeByQNamed(QNamed $type): XsdType
    {
        $namespace = $this->namespaces->lookupNamespaceByQname($type)->unwrapOr('');

        return XsdType::guess($type->localName)
            ->withXmlNamespaceName($type->prefix)
            ->withXmlNamespace($namespace)
            ->withXmlTypeName($type->localName)
            ->withMeta(
                fn (TypeMeta $meta): TypeMeta => $meta
                    ->withIsSimple($this->guessIfQnamedIsSimple($namespace, $type))
            );
    }

    private function guessIfQNamedIsSimple(string $namespace, QNamed $type): bool
    {
        return !(
            Detector\Soap11ArrayDetector::detect($namespace, $type->localName)
            || Detector\Soap12ArrayDetector::detect($namespace, $type->localName)
            || Detector\Soap11StructDetector::detect($namespace, $type->localName)
            || Detector\Soap12StructDetector::detect($namespace, $type->localName)
            || Detector\ApacheMapDetector::detect($namespace, $type->localName)
        );
    }

    private function createAnyType(): XsdType
    {
        $namespace = Xmlns::xsd()->value();

        return XsdType::any()
            ->withXmlNamespaceName($this->namespaces->lookupNameFromNamespace($namespace)->unwrapOr(''));
    }
}
