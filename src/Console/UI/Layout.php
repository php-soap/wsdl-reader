<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI;

use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Soap\WsdlReader\Console\UI\Components\Help;
use Soap\WsdlReader\Console\UI\Components\Navigation;

final readonly class Layout
{
    public static function create(UIState $state): Widget
    {
        return GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::min(3),
                Constraint::percentage(100),
                Constraint::min(3),
            )
            ->widgets(
                Navigation::create($state),
                $state->currentPage->build(),
                Help::create(...$state->currentPage->help()),
            );
    }
}
