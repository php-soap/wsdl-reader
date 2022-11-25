<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Xml;

class QnameParser
{
    /**
     * @param non-empty-string $qname
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
