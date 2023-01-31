<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Inheritance\Restriction;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Vec\map;

final class RestrictionsConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof Restriction) {
            return $metaType;
        }

        return $metaType
            ->withMeta([
                ...$metaType->getMeta(),
                ...$this->parseChecks($xsdType),
            ]);
    }

    private function parseChecks(Restriction $restriction): array
    {
        $checks = $restriction->getChecks();
        if (!$checks) {
            return [];
        }

        $data = ['restriction' => $restriction->getChecks()];

        if ($enumerations = $checks['enumeration'] ?? []) {
            $data = [
                ...$data,
                'enums' => map($enumerations, static fn (array $enum): string => (string)($enum['value'] ?? '')),
            ];
        }

        return $data;
    }
}
