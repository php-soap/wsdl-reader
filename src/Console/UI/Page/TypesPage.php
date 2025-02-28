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
use Soap\Engine\Metadata\Model\Type;
use Soap\WsdlReader\Console\UI\Components\MetaTable;
use Soap\WsdlReader\Console\UI\Components\Search;
use Soap\WsdlReader\Console\UI\Page;
use Soap\WsdlReader\Console\UI\UIState;
use Soap\WsdlReader\Formatter\LongTypeFormatter;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\map;

final readonly class TypesPage implements Page
{
    private TypesPageState $pageState;

    public function __construct(
        UIState $UIState,
    ) {
        $this->pageState = new TypesPageState($UIState);
    }

    public function title(): string
    {
        return '<fg=red>[t]</>ypes';
    }

    public function navigationChar(): string
    {
        return 't';
    }

    public function build(): Widget
    {
        $selectedType = $this->pageState->selectedType();
        $selectedProperty = $this->pageState->selectedTypeProp();

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
                                'Types (%s/%s)',
                                $this->pageState->totalFilteredTypes(),
                                $this->pageState->totalTypes(),
                            )))
                            ->borders(Borders::ALL)
                            ->widget(
                                ListWidget::default()
                                    ->items(
                                        ...map(
                                            $this->pageState->types(),
                                            static fn (Type $type) => ListItem::new(Text::fromString($type->getName()))
                                        )
                                    )
                                    ->select($this->pageState->selectedTypeIndex())
                                    ->highlightStyle(Style::default()->white()->onBlue())
                            ),
                        $selectedType
                            ? GridWidget::default()
                            ->constraints(
                                ...(
                                    $this->pageState->zoom !== null
                                        ? [Constraint::percentage(100)]
                                        : [
                                            Constraint::percentage(30),
                                            Constraint::percentage(35),
                                            Constraint::percentage(35),
                                        ]
                                )
                            )
                            ->widgets(...filter_nulls([
                                $this->pageState->renderWidgetAtZoom(
                                    1,
                                    BlockWidget::default()
                                    ->titles(Title::fromLine(Line::parse('<fg=red>1.</> Type signature')))
                                    ->borders(Borders::ALL)
                                    ->padding(Padding::horizontal(1))
                                    ->widget(
                                        ParagraphWidget::fromText(
                                            Text::fromString(
                                                (new LongTypeFormatter())($selectedType)
                                            )
                                        ),
                                    )
                                ),
                                $this->pageState->renderWidgetAtZoom(
                                    2,
                                    BlockWidget::default()
                                    ->titles(Title::fromLine(Line::parse('<fg=red>2.</> Type metadata')))
                                    ->borders(Borders::ALL)
                                    ->padding(Padding::horizontal(1))
                                    ->widget(
                                        MetaTable::create(match($this->pageState->informationType) {
                                            InformationType::Meta => $selectedType->getXsdType()->getMeta(),
                                            InformationType::Type => $selectedType->getXsdType(),
                                        })
                                    ),
                                ),
                                $this->pageState->renderWidgetAtZoom(
                                    3,
                                    BlockWidget::default()
                                    ->titles(
                                        Title::fromLine(Line::parse('<fg=red>3.</> ' . match(true) {
                                            $selectedProperty !== null => '← Property ' . $selectedProperty->getName() . ' → ',
                                            default => 'No properties',
                                        })),
                                    )
                                    ->borders(Borders::ALL)
                                    ->padding(Padding::horizontal(1))
                                    ->widget(
                                        $selectedProperty
                                            ? MetaTable::create(match($this->pageState->informationType) {
                                                InformationType::Meta => $selectedProperty->getType()->getMeta(),
                                                InformationType::Type => $selectedProperty->getType(),
                                            })
                                            : ParagraphWidget::fromText(Text::fromString(''))
                                    ),
                                ),
                            ]))
                            : BlockWidget::default()
                            ->titles(Title::fromString('EMPTY'))
                            ->borders(Borders::ALL)
                            ->padding(Padding::horizontal(1))
                            ->widget(
                                ParagraphWidget::fromText(
                                    Text::fromString('No type could be found')
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
