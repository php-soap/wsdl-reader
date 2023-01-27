<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Exception;

use RuntimeException;
use Soap\WsdlReader\Model\Definitions\SoapVersion;

final class ServiceException extends RuntimeException
{
    public static function notFound(?SoapVersion $preferredSoapVersion): self
    {
        return new self(
            sprintf(
                'Unable to find a usable %sservice inside your WSDL.',
                $preferredSoapVersion?->humanReadable() ?? ''
            )
        );
    }
}
