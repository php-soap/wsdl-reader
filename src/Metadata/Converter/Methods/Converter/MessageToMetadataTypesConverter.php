<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Converter;

use Soap\Engine\Exception\MetadataException;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Model\Definitions\Message;
use Soap\WsdlReader\Model\Definitions\Part;
use Soap\WsdlReader\Model\Definitions\QNamed;
use function Psl\Dict\pull;

final class MessageToMetadataTypesConverter
{
    public function __construct(
        private TypeCollection $knownTypes
    ) {
    }

    /**
     * @return array<string, XsdType>
     */
    public function __invoke(Message $message): array
    {
        return pull(
            $message->parts->items,
            fn (Part $part): XsdType => $this->findXsdType($part->element),
            static fn (Part $message): string => $message->name
        );
    }

    /**
     * // Todo : make sure namespaces match, currently just looking for name is not sufficient!!
     * // TODO : make sure simple types contain all info we have ...
     */
    private function findXsdType(QNamed $type): XsdType
    {
        try {
            return $this->knownTypes->fetchFirstByName($type->localName)->getXsdType();
        } catch (MetadataException $e) {
            // Proxy to simple/base type ...
            return XsdType::guess($type->localName)
                ->withXmlNamespaceName($type->prefix)
                ->withXmlNamespace('TODO lookup')
                ->withMeta([]);
        }
    }
}
