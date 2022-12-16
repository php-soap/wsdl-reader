<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Formatter;

use Soap\Engine\Metadata\Model\XsdType;

class XsdTypeFormatter
{
    public function __invoke(XsdType $xsdType): string
    {
        return $xsdType->getName();
    }
}