<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Inheritance\Restriction;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\string;
use function Psl\Type\vec;
use function Psl\Vec\map;

final class RestrictionsConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof Restriction) {
            return $engineType;
        }

        return $engineType
            ->withMeta(
                fn (TypeMeta $meta): TypeMeta => $this->parseChecks($meta, $xsdType)
            );
    }

    private function parseChecks(TypeMeta $meta, Restriction $restriction): TypeMeta
    {
        $checks = $restriction->getChecks();
        if (!$checks) {
            return $meta;
        }

        $meta = $meta->withRestriction($restriction->getChecks());
        if ($enumerations = $checks['enumeration'] ?? []) {
            $meta = $meta->withEnums(
                map(
                    vec(shape([
                        'value' => optional(string()),
                    ], true))->coerce($enumerations),
                    static fn (array $enum): string => $enum['value'] ?? ''
                )
            );
        }

        return $meta;
    }
}
