<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Configurator\Operation;

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\WsdlReader\Model\Definitions\BindingOperation;
use Soap\WsdlReader\Model\Definitions\BindingStyle;
use Soap\WsdlReader\Model\Definitions\BindingUse;
use Soap\WsdlReader\Model\Definitions\EncodingStyle;
use Soap\WsdlReader\Model\Definitions\Implementation\Message\HttpMessage;
use Soap\WsdlReader\Model\Definitions\Implementation\Operation\HttpOperation;
use Soap\WsdlReader\Model\Definitions\SoapVersion;

final class HttpBindingOperationConfigurator
{
    public function __invoke(Method $method, BindingOperation $operation): Method
    {
        $implementation = $operation->implementation;
        if (!$implementation instanceof HttpOperation) {
            return $method;
        }

        if (!$this->messageIsConsideredSoap($operation)) {
            return $method;
        }

        $guessedSoapVersion = $implementation->transport->guessSoapVersion();
        $guessedBindingUse = $this->guessBindingUse($guessedSoapVersion);
        $guessedEncodingStyle = $this->guessEncodingStyle($guessedSoapVersion);

        return $method->withMeta(
            static fn (MethodMeta $meta): MethodMeta => $meta
                ->withSoapVersion($guessedSoapVersion?->value)
                ->withAction('') // Soap Action is considered empty for HTTP binding.
                ->withOperationName($operation->name)
                ->withBindingStyle(BindingStyle::RPC->value)
                ->withInputBindingUsage($guessedBindingUse?->value)
                ->withInputEncodingStyle($guessedEncodingStyle?->value)
                ->withOutputBindingUsage($guessedBindingUse?->value)
                ->withOutputEncodingStyle($guessedEncodingStyle?->value)
        );
    }

    private function messageIsConsideredSoap(BindingOperation $operation): bool
    {
        $input = $operation->input?->implementation;
        if (!$input instanceof HttpMessage) {
            return false;
        }

        return $input->isConsideredSoapContentType();
    }

    private function guessEncodingStyle(?SoapVersion $soapVersion): ?EncodingStyle
    {
        return match ($soapVersion) {
            SoapVersion::SOAP_11 => EncodingStyle::SOAP_11,
            SoapVersion::SOAP_12 => EncodingStyle::SOAP_12_2003_05,
            default => null,
        };
    }

    private function guessBindingUse(?SoapVersion $soapVersion): ?BindingUse
    {
        return match ($soapVersion) {
            SoapVersion::SOAP_11 => BindingUse::ENCODED,
            SoapVersion::SOAP_12 => BindingUse::LITERAL,
            default => null,
        };
    }
}
