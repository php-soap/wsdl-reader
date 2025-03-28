<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components\Wsdl;

use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\Table\TableCell;
use PhpTui\Tui\Extension\Core\Widget\Table\TableRow;
use PhpTui\Tui\Extension\Core\Widget\TableWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Widget;
use function Psl\Vec\map_with_key;

final readonly class NamespacesWidget
{
    public static function create(NamespacesState $state): Widget
    {
        $headerStyle = Style::default()->bold();

        return BlockWidget::default()
            ->titles(Title::fromString(sprintf(
                'Namespaces (%s/%s)',
                $state->totalFilteredNamespaces(),
                $state->totalNamespaces(),
            )))
            ->borders(Borders::ALL)
            ->widget(
                TableWidget::default()
                    ->widths(
                        Constraint::percentage(25),
                        Constraint::percentage(75),
                    )

                    ->header(
                        TableRow::fromCells(
                            new TableCell(Text::fromLine(Line::fromString('Prefix')), $headerStyle),
                            new TableCell(Text::fromLine(Line::fromString('Namespace')), $headerStyle),
                        )
                    )
                    ->rows(...map_with_key(
                        $state->namespaces(),
                        static fn (string $namespace, string $prefix) => TableRow::fromCells(
                            TableCell::fromString($prefix),
                            TableCell::fromString($namespace),
                        )
                    ))
                    ->select($state->currentNamespaceIndex())
                    ->highlightStyle(Style::default()->white()->onBlue())
            );
    }
}
