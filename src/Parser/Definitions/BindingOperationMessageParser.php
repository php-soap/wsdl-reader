<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Psl\Type;
use Soap\WsdlReader\Model\Definitions\BindingOperationMessage;
use Soap\WsdlReader\Model\Definitions\BindingOperationMessages;
use Soap\WsdlReader\Model\Definitions\BindingUse;
use Soap\WsdlReader\Model\Definitions\SoapVersion;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Collection\NodeList;
use VeeWee\Xml\Dom\Document;
use function Psl\Result\wrap;

final class BindingOperationMessageParser
{
    public function __invoke(Document $wsdl, DOMElement $message, SoapVersion $soapVersion): BindingOperationMessage
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        $soapVersionPrefix = $soapVersion->wsdlPresetName();

        return new BindingOperationMessage(
            name: $xpath->evaluate('string(./@name)', Type\string(), $message),
            bindingUse: BindingUse::tryFrom(
                $xpath->evaluate('string(./'.$soapVersionPrefix.':body/@use)', Type\string(), $message)
            ) ?? BindingUse::LITERAL,
        );
    }

    public static function tryParseFromOptionalSingleOperationMessage(
        Document $wsdl,
        DOMElement $operation,
        string $message,
        SoapVersion $soapVersion
    ): ?BindingOperationMessage {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        return wrap(static fn () => $xpath->querySingle('./wsdl:'.$message, $operation))
            ->proceed(
                static fn (DOMElement $messageElement): BindingOperationMessage =>
                    (new self())($wsdl, $messageElement, $soapVersion),
                static fn () => null
            );
    }

    public static function tryParseList(Document $wsdl, NodeList $list, SoapVersion $soapVersion): BindingOperationMessages
    {
        return new BindingOperationMessages(
            ...$list->map(
                static fn (DOMElement $message): BindingOperationMessage => (new self)($wsdl, $message, $soapVersion)
            )
        );
    }
}
