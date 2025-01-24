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
}
