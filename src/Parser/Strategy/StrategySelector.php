<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Strategy;

use Soap\WsdlReader\Model\Definitions\AddressBindingType;

final class StrategySelector
{
    public function __invoke(AddressBindingType $type): StrategyInterface
    {
        return match ($type) {
            AddressBindingType::SOAP_11 => new SoapStrategy(),
            AddressBindingType::SOAP_12 => new SoapStrategy(),
            AddressBindingType::RPC => new SoapStrategy(),
            AddressBindingType::HTTP_11 => new HttpStrategy(),
            AddressBindingType::HTTP_12 => new HttpStrategy(),
        };
    }
}
