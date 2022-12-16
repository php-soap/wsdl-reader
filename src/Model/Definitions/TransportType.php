<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

enum TransportType: string
{
    case HTTP = 'http://schemas.xmlsoap.org/soap/http';
}
