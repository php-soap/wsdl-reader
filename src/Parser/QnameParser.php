<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Parser;

final class QnameParser
{
    /**
     * @return array{0: string, 1: string}
     */
    public function __invoke(string $qname): array
    {
        if (strpos($qname, ':') === false) {
            return ['', $qname];
        }

        return explode(':', $qname, 2);
    }
}
