<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMElement;
use Psl\Type;
use Soap\WsdlReader\Model\Definitions\Param;
use Soap\WsdlReader\Model\Definitions\Params;
use Soap\WsdlReader\Model\Definitions\QNamed;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Collection\NodeList;
use VeeWee\Xml\Dom\Document;
use function Psl\Result\wrap;
use function VeeWee\Xml\Dom\Assert\assert_element;

final class OperationParamParser
{
    public function __invoke(Document $wsdl, DOMElement $operation): Param
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return new Param(
            name: $xpath->evaluate('string(./@name)', Type\string(), $operation),
            message: QNamed::parse($xpath->evaluate('string(./@message)', Type\string(), $operation))
        );
    }

    public static function tryParseOptionally(Document $wsdl, string $message, DOMElement $operation): ?Param
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        return wrap(static fn (): DOMElement => assert_element($xpath->querySingle('./wsdl:'.$message, $operation)))
            ->proceed(
                static fn (DOMElement $messageElement): Param =>
                    (new self())($wsdl, $messageElement),
                static fn () => null
            );
    }

    /**
     * @param NodeList<DOMElement> $params
     */
    public static function tryParseList(Document $wsdl, NodeList $params): Params
    {
        return new Params(
            ...$params->map(
                static fn (DOMElement $param): Param => (new self)($wsdl, $param)
            )
        );
    }
}
