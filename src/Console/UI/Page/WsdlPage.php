<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Page;

use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Soap\WsdlReader\Console\UI\Components\Search;
use Soap\WsdlReader\Console\UI\Page;
use Soap\WsdlReader\Console\UI\UIState;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\map_with_key;

final readonly class WsdlPage implements Page
{
    private WsdlPageState $pageState;

    public function __construct(
        UIState $UIState,
    ) {
        $this->pageState = new WsdlPageState($UIState);
    }

    public function title(): string
    {
        return '<fg=red>[w]</>sdl';
    }

    public function navigationChar(): string
    {
        return 'w';
    }

    public function build(): Widget
    {
        return GridWidget::default()
            ->constraints(...filter_nulls([
                Constraint::percentage(100),
                $this->pageState->searching() ? Constraint::min(3) : null,
            ]))
            ->widgets(...filter_nulls([
                GridWidget::default()
                    ->direction(Direction::Horizontal)
                    ->constraints(
                        Constraint::percentage(15),
                        Constraint::percentage(85),
                    )
                    ->widgets(
                        BlockWidget::default()
                            ->titles(Title::fromString('WSDL section'))
                            ->borders(Borders::ALL)
                            ->widget(
                                ListWidget::default()
                                    ->items(
                                        ...map_with_key(
                                            WsdlInfo::cases(),
                                            static fn (int $index, WsdlInfo $info) => ListItem::new(
                                                Text::fromLine(Line::parse('<fg=red>'.($index + 1).'.</> '. $info->value))
                                            )
                                        )
                                    )
                                    ->select($this->pageState->infoPart->index())
                                    ->highlightStyle(Style::default()->white()->onBlue())
                            ),
                        $this->pageState->buildInfoWidget(),
                    ),
                $this->pageState->search && $this->pageState->searching() ? Search::create($this->pageState->search) : null,
            ]));
    }

    public function handle(Event $event): void
    {
        $this->pageState->handle($event);
    }

    public function isBlockingParentEvents(): bool
    {
        return $this->pageState->searching();
    }

    public function help(): array
    {
        return [
            Line::parse('↑'),
            Line::parse('↓'),
            Line::parse('/ (search)'),
        ];
    }
}
