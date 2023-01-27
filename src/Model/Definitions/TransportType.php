<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

enum TransportType: string
{
    case HTTP = 'http://schemas.xmlsoap.org/soap/http';
    case W3_HTTP = 'http://www.w3.org/2003/05/soap/bindings/HTTP/';
    case SMTP = 'http://schemas.xmlsoap.org/soap/smtp';

    public function isHttp(): bool
    {
        return $this === self::HTTP || $this === self::W3_HTTP;
    }
}
