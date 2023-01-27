<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

enum AddressBindingType : string
{
    case SOAP_11 = 'http://schemas.xmlsoap.org/wsdl/soap/';
    case SOAP_12 = 'http://schemas.xmlsoap.org/wsdl/soap12/';
    case RPC = 'http://www.w3.org/2003/05/soap-rpc"';
    case HTTP_11 = 'http://schemas.xmlsoap.org/wsdl/http/';
    case HTTP_12 = 'http://www.w3.org/2003/05/soap/bindings/HTTP/"';

    public function isSoap(): bool
    {
        return $this === self::SOAP_11 || $this === self::SOAP_12 || $this === self::RPC;
    }

    public function isHttp(): bool
    {
        return $this === self::HTTP_11 || $this === self::HTTP_12;
    }

    public function soapVersion(): ?SoapVersion
    {
        return match ($this) {
            self::SOAP_11 => SoapVersion::SOAP_11,
            self::SOAP_12, self::RPC => SoapVersion::SOAP_12,
            default => null,
        };
    }
}
