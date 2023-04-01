<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Formatter;

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Parameter;
use function Psl\Str\format;

final class LongMethodFormatter
{
    private XsdTypeFormatter $xsdTypeFormatter;

    public function __construct()
    {
        $this->xsdTypeFormatter = new XsdTypeFormatter();
    }

    public function __invoke(Method $method): string
    {
        return format(
            '%s(%s): %s',
            $method->getName(),
            implode(', ', $method->getParameters()->map(
                fn (Parameter $parameter): string => format(
                    '%s $%s',
                    ($this->xsdTypeFormatter)($parameter->getType()),
                    $parameter->getName()
                )
            )),
            ($this->xsdTypeFormatter)($method->getReturnType())
        );
    }
}
