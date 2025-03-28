<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components;

use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\TabsWidget;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Widget;
use Soap\WsdlReader\Console\UI\Page;
use Soap\WsdlReader\Console\UI\UIState;
use function Psl\Vec\keys;
use function Psl\Vec\map;

final class Navigation
{
    public static function create(UIState $state): Widget
    {
        return BlockWidget::default()
            ->borders(Borders::ALL)->style(Style::default()->white())
            ->widget(
                TabsWidget::fromTitles(
                    Line::parse('<fg=red>[q]</>uit'),
                    ...map(
                        $state->availablePages,
                        static fn (Page $page) => Line::parse($page->title())
                    )
                )
                ->select((int)array_search(get_class($state->currentPage), keys($state->availablePages), true) + 1)
                ->highlightStyle(Style::default()->white()->onBlue())
            );
    }
}
