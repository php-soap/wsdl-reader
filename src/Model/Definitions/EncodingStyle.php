<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

enum EncodingStyle: string
{
    case SOAP_11 = 'http://schemas.xmlsoap.org/soap/encoding/';
    case SOAP_12 = 'http://www.w3.org/2001/12/soap-encoding';
}
