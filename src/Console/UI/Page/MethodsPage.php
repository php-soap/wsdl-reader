<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Page;

use PhpTui\Term\Event;
use PhpTui\Tui\Extension\Core\Widget\Block\Padding;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Line;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Soap\Engine\Metadata\Model\Method;
use Soap\WsdlReader\Console\UI\Components\MetaTable;
use Soap\WsdlReader\Console\UI\Components\Search;
use Soap\WsdlReader\Console\UI\Page;
use Soap\WsdlReader\Console\UI\UIState;
use Soap\WsdlReader\Formatter\LongMethodFormatter;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\map;

final readonly class MethodsPage implements Page
{
    private MethodsPageState $pageState;

    public function __construct(
        UIState $UIState,
    ) {
        $this->pageState = new MethodsPageState($UIState);
    }

    public function title(): string
    {
        return '<fg=red>[m]</>ethods';
    }

    public function navigationChar(): string
    {
        return 'm';
    }

    public function build(): Widget
    {
        $selectedMethod = $this->pageState->selectedMethod();
        $selectedParamName = $this->pageState->selectedMethodParamName();
        $selectedParamType = $this->pageState->selectedMethodParamType();

        return GridWidget::default()
            ->constraints(...filter_nulls([
                Constraint::percentage(100),
                $this->pageState->searching() ? Constraint::min(3) : null,
            ]))
            ->widgets(...filter_nulls([
                GridWidget::default()
                    ->direction(Direction::Horizontal)
                    ->constraints(
                        Constraint::percentage(25),
                        Constraint::min(3),
                    )
                    ->widgets(
                        BlockWidget::default()
                            ->titles(Title::fromString(sprintf(
                                'Methods (%s/%s)',
                                $this->pageState->totalFilteredMethods(),
                                $this->pageState->totalMethods(),
                            )))
                            ->borders(Borders::ALL)
                            ->widget(
                                ListWidget::default()
                                    ->items(
                                        ...map(
                                            $this->pageState->methods(),
                                            static fn (Method $method) => ListItem::new(Text::fromString($method->getName()))
                                        )
                                    )
                                    ->select($this->pageState->selectedMethodIndex())
                                    ->highlightStyle(Style::default()->white()->onBlue())
                            ),
                        $selectedMethod
                            ? GridWidget::default()
                                ->constraints(
                                    ...(
                                        $this->pageState->zoom !== null
                                        ? [Constraint::percentage(100)]
                                        : [
                                            Constraint::percentage(20),
                                            Constraint::percentage(35),
                                            Constraint::percentage(35),
                                        ]
                                    )
                                )
                                ->widgets(...filter_nulls([
                                    $this->pageState->renderWidgetAtZoom(
                                        1,
                                        BlockWidget::default()
                                        ->titles(Title::fromLine(Line::parse('<fg=red>1.</> Method signature')))
                                        ->borders(Borders::ALL)
                                        ->padding(Padding::horizontal(1))
                                        ->widget(
                                            ParagraphWidget::fromText(
                                                Text::fromString(
                                                    (new LongMethodFormatter())($selectedMethod)
                                                )
                                            ),
                                        ),
                                    ),
                                    $this->pageState->renderWidgetAtZoom(
                                        2,
                                        BlockWidget::default()
                                        ->titles(Title::fromLine(Line::parse('<fg=red>2.</> Method Metadata')))
                                        ->borders(Borders::ALL)
                                        ->padding(Padding::horizontal(1))
                                        ->widget(
                                            MetaTable::create($selectedMethod->getMeta())
                                        ),
                                    ),
                                    $this->pageState->renderWidgetAtZoom(
                                        3,
                                        BlockWidget::default()
                                        ->titles(
                                            Title::fromLine(Line::parse('<fg=red>3.</> ' . match(true) {
                                                $selectedParamName !== null => '← Parameter ' . $selectedParamName . ' → ',
                                                $selectedParamType !== null => '← Result type '.$selectedParamType->getName().' → ',
                                                default => 'No parameters or result types found',
                                            }))
                                        )
                                        ->borders(Borders::ALL)
                                        ->padding(Padding::horizontal(1))
                                        ->widget(
                                            $selectedParamType
                                                ? MetaTable::create(match($this->pageState->informationType) {
                                                    InformationType::Meta => $selectedParamType->getMeta(),
                                                    InformationType::Type => $selectedParamType,
                                                })
                                                : ParagraphWidget::fromText(Text::fromString(''))
                                        )
                                    ),
                                ]))
                            : BlockWidget::default()
                                ->titles(Title::fromString('EMPTY'))
                                ->borders(Borders::ALL)
                                ->padding(Padding::horizontal(1))
                                ->widget(
                                    ParagraphWidget::fromText(
                                        Text::fromString('No method could be found')
                                    ),
                                ),
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
            Line::parse('←'),
            Line::parse('→'),
            Line::parse('Tab ('.$this->pageState->informationType->toggle()->value.')'),
            Line::parse('/ (search)'),
        ];
    }
}
