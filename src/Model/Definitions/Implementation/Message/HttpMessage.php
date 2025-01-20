<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions\Implementation\Message;

final class HttpMessage implements MessageImplementation
{
    public function __construct(
        public readonly string $contentType,
        public readonly ?string $part,
    ) {
    }

    public function isConsideredSoapContentType(): bool
    {
        return match(mb_strtolower($this->contentType)) {
            'application/xml' => true,
            'application/text' => true,
            'application/soap+xml' => true,
            'multipart/related' => true,
            default => false,
        };
    }
}
