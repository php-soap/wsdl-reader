<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components;

use PhpTui\Tui\Extension\Core\Widget\Table\TableCell;
use PhpTui\Tui\Extension\Core\Widget\Table\TableRow;
use PhpTui\Tui\Extension\Core\Widget\TableWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Widget;
use Psl\Option\Option;
use ReflectionClass;
use ReflectionProperty;
use Throwable;
use function Psl\Dict\filter_nulls;
use function Psl\Json\encode;
use function Psl\Vec\map;

final readonly class MetaTable
{
    public static function create(object $meta): Widget
    {
        $headerStyle = Style::default()->bold();

        return TableWidget::default()
            ->widths(
                Constraint::min(50),
                Constraint::percentage(100),
            )

            ->header(
                TableRow::fromCells(
                    new TableCell(Text::fromLine(Line::fromString('Key')), $headerStyle),
                    new TableCell(Text::fromLine(Line::fromString('Value')), $headerStyle),
                )
            )
            ->rows(...map(
                self::buildKeyPairs($meta),
                static fn ($current) => TableRow::fromCells(
                    TableCell::fromString($current[0]),
                    TableCell::fromString($current[1]),
                )
            ));
    }

    /**
     * @return array<array{0: non-empty-string, 1: string}>
     */
    private static function buildKeyPairs(object $object): array
    {
        $rc = new ReflectionClass($object);

        return filter_nulls(
            map(
                $rc->getProperties(),
                /** @return array{0: non-empty-string, 1: string}|null */
                static function (ReflectionProperty $prop) use ($object): ?array {
                    $value = self::tryStringifyValue($prop->getValue($object));
                    if ($value === null) {
                        return null;
                    }

                    return [$prop->getName(), $value];
                }
            )
        );
    }

    private static function tryStringifyValue(mixed $value): ?string
    {
        try {
            return match (true) {
                is_array($value) => encode($value, pretty: false, flags: JSON_UNESCAPED_SLASHES),
                is_bool($value) => $value ? 'true' : 'false',
                is_scalar($value) => (string)$value,
                $value instanceof Option => $value->map(self::tryStringifyValue(...))->unwrapOr(null),
                default => null,
            };
        } catch (Throwable) {
            return null;
        }
    }
}
