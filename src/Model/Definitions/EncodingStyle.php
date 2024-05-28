<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use function in_array;

enum EncodingStyle: string
{
    case SOAP_11 = 'http://schemas.xmlsoap.org/soap/encoding/';
    case SOAP_12_2001_09 = 'http://www.w3.org/2001/09/soap-encoding';
    case SOAP_12_2001_12 = 'http://www.w3.org/2001/12/soap-encoding';
    case SOAP_12_2003_05 = 'http://www.w3.org/2003/05/soap-encoding';

    /**
     * @return list<string>
     */
    public static function listKnownSoap12Version(): array
    {
        return [
            self::SOAP_12_2001_09->value,
            self::SOAP_12_2001_12->value,
            self::SOAP_12_2003_05->value
        ];
    }

    public static function isSoap12Encoding(string $namespace): bool
    {
        return in_array($namespace, self::listKnownSoap12Version(), true);
    }
}
