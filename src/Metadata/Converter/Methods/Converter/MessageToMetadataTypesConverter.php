<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Converter;

use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Model\Definitions\Message;
use Soap\WsdlReader\Model\Definitions\Part;
use function Psl\Dict\pull;

class MessageToMetadataTypesConverter
{
    public function __construct(
        private TypeCollection $knownTypes
    ){
    }

    /**
     * // Todo : make sure namespaces match, currently just looking for name is not sufficient!!
     * // Todo : what for simple Types in message element? Maybe better to load xsd from wsdl info instead from collected types?
     *
     * @param Message $message
     * @return array<string, XsdType>
     */
    public function __invoke(Message $message): array
    {
        return pull(
            $message->parts->items,
            fn (Part $part): XsdType => $this->knownTypes->fetchFirstByName($part->element->localName)->getXsdType(),
            static fn (Part $message): string => $message->name
        );
    }
}
