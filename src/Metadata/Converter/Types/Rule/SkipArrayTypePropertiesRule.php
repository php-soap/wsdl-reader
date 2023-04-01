<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Rule;

use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

/**
 * This rule can be used to skip property generation for e.g. array types that will be converted in the encoder/decoder.
 *
 */
final class SkipArrayTypePropertiesRule
{
    public function __invoke(Type $type, TypesConverterContext $context): bool
    {
        if (!$base = $type->getParent()?->getBase()) {
            return false;
        }

        if (!$context->isBaseSchema($base->getSchema())) {
            return false;
        }

        if ('array' === mb_strtolower($base->getName() ?? '')) {
            return true;
        }

        return false;
    }
}
