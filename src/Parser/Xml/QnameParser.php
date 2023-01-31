<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Xml;

final class QnameParser
{
    /**
     * @return array{0: string, 1: string}
     */
    public function __invoke(string $qname): array
    {
        if (!str_contains($qname, ':')) {
            return ['', $qname];
        }

        $parts = explode(':', $qname, 2);

        return [$parts[0], $parts[1] ?? ''];
    }
}
