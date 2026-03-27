<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use Dom\Element;
use Psl\Type;
use Soap\WsdlReader\Model\Definitions\BindingOperationMessage;
use Soap\WsdlReader\Model\Definitions\BindingOperationMessages;
use Soap\WsdlReader\Parser\Strategy\StrategyInterface;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Collection\NodeList;
use VeeWee\Xml\Dom\Document;
use function Psl\Result\wrap;
use function VeeWee\Xml\Dom\Assert\assert_element;

final class BindingOperationMessageParser
{
    public function __invoke(Document $wsdl, Element $message, StrategyInterface $strategy): BindingOperationMessage
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new BindingOperationMessage(
            name: $xpath->evaluate('string(./@name)', Type\string(), $message),
            implementation: $strategy->parseMessageImplementation($wsdl, $message)
        );
    }

    public static function tryParseFromOptionalSingleOperationMessage(
        Document $wsdl,
        Element $operation,
        string $message,
        StrategyInterface $strategy
    ): ?BindingOperationMessage {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return wrap(
            static fn (): Element => assert_element($xpath->querySingle('./wsdl:'.$message, $operation))
        )->proceed(
            static fn (Element $messageElement): BindingOperationMessage =>
                (new self())($wsdl, $messageElement, $strategy),
            static fn () => null
        );
    }

    /**
     * @param NodeList<Element> $list
     */
    public static function tryParseList(Document $wsdl, NodeList $list, StrategyInterface $strategy): BindingOperationMessages
    {
        return new BindingOperationMessages(
            ...$list->map(
                static fn (Element $message): BindingOperationMessage => (new self)($wsdl, $message, $strategy)
            )
        );
    }
}
