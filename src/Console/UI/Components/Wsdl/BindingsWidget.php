<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components\Wsdl;

use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Soap\WsdlReader\Console\UI\Components\ScrollableTextArea;
use Soap\WsdlReader\Model\Definitions\Binding;
use function Psl\Vec\map;

final readonly class BindingsWidget
{
    public static function create(BindingsState $state): Widget
    {
        return GridWidget::default()
            ->direction(Direction::Horizontal)
            ->constraints(
                Constraint::percentage(20),
                Constraint::percentage(80),
            )
            ->widgets(
                BlockWidget::default()
                    ->titles(Title::fromString(sprintf(
                        'Bindings (%s/%s)',
                        $state->totalFilteredBindings(),
                        $state->totalBindings(),
                    )))
                    ->borders(Borders::ALL)
                    ->widget(
                        ListWidget::default()
                            ->items(
                                ...map(
                                    $state->bindings(),
                                    static fn (Binding $info) => ListItem::new(Text::fromString($info->name))
                                )
                            )
                            ->select($state->currentBindingIndex())
                            ->highlightStyle(Style::default()->white()->onBlue())
                    ),
                BlockWidget::default()
                    ->titles(Title::fromString('Details (scrollable)'))
                    ->borders(Borders::ALL)
                    ->widget(ScrollableTextArea::create($state->textAreaState)),
            );
    }
}
