<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

enum SoapVersion: string
{
    case SOAP_11 = 'http://schemas.xmlsoap.org/wsdl/soap/';
    case SOAP_12 = 'http://schemas.xmlsoap.org/wsdl/soap12/';

    public function wsdlPresetName(): string
    {
        return match ($this) {
            self::SOAP_11 => 'soap',
            self::SOAP_12 => 'soap12',
        };
    }

    public function humanReadable(): string
    {
        return match ($this) {
            self::SOAP_11 => 'SOAP 1.1',
            self::SOAP_12 => 'SOAP 1.2',
        };
    }
}
