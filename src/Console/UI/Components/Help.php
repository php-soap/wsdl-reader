<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components;

use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\TabsWidget;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Widget;

final class Help
{
    public static function create(Line ... $lines): Widget
    {
        return BlockWidget::default()
            ->borders(Borders::ALL)->style(Style::default()->white())
            ->widget(
                TabsWidget::fromTitles(
                    ...$lines
                )
            );
    }
}
