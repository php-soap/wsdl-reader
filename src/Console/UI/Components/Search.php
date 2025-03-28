<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components;

use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Widget;

final class Search
{
    public static function create(SearchState $state): Widget
    {
        return BlockWidget::default()
            ->titles(Title::fromString('Search'))
            ->borders(Borders::ALL)
            ->widget(
                ParagraphWidget::fromLines(Line::fromString($state->query)),
            );
    }
}
