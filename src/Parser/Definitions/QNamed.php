<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use Soap\WsdlReader\Parser\Xml\QnameParser;

class QNamed
{
    private function __construct(
        public readonly string $qname,
        public readonly string $prefix,
        public readonly string $localName,
    ){
    }

    public static function parse(string $qname): self
    {
        [$prefix, $localName] = (new QnameParser())($qname);

        return new self($qname, $prefix, $localName);
    }

    public function isPrefixed(): bool
    {
        return $this->prefix !== '';
    }
}
