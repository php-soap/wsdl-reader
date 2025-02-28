<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Page;

use PhpTui\Term\Event;
use PhpTui\Term\KeyCode;
use PhpTui\Tui\Widget\Widget;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\Type;
use Soap\WsdlReader\Console\UI\Components\SearchState;
use Soap\WsdlReader\Console\UI\EventHandler;
use Soap\WsdlReader\Console\UI\UIState;
use function Psl\Math\max;
use function Psl\Math\min;
use function Psl\Vec\filter;
use function Psl\Vec\values;

final class TypesPageState implements EventHandler
{
    public function __construct(
        public UIState $state,
        private int $selectedType = 0,
        private int $selectedTypeProp = 0,
        public ?SearchState $search = null,
        public InformationType $informationType = InformationType::Type,
        public ?int $zoom = null,
    ) {
    }

    public function selectedTypeIndex(): int
    {
        return $this->selectedType;
    }

    public function selectedType(): ?Type
    {
        return values($this->types())[$this->selectedType] ?? null;
    }

    public function selectedTypeProp(): ?Property
    {
        $selectedType = $this->selectedType();
        if (!$selectedType) {
            return null;
        }

        return values($selectedType->getProperties())[$this->selectedTypeProp] ?? null;
    }

    /**
     * @return list<Type>
     */
    public function types(): array
    {
        $query = (string) $this->search?->query;

        return \Psl\Vec\sort(
            filter(
                $this->state->metadata->getTypes(),
                static fn (Type $type) => !$query || mb_stripos($type->getName(), $query) !== false
            ),
            static fn (Type $a, Type $b) => $a->getName() <=> $b->getName()
        );
    }

    public function totalTypes(): int
    {
        return count($this->state->metadata->getTypes());
    }

    public function totalFilteredTypes(): int
    {
        return count($this->types());
    }

    public function up(): void
    {
        $this->selectedType = (int) max([$this->selectedType-1, 0]);
        $this->selectedTypeProp = 0;
    }

    public function down(): void
    {
        $this->selectedType = (int) min([$this->selectedType + 1, count($this->types()) - 1]);
        $this->selectedTypeProp = 0;
    }

    public function searching(): bool
    {
        return $this->search !== null && $this->search->locked === false;
    }

    public function reset(): void
    {
        $this->stopSearching();
        $this->zoom(null);
    }

    public function startSearching(): void
    {
        $this->search ??= SearchState::empty();
        $this->search->unlock();
    }

    public function lockSearching(): void
    {
        if ($this->search) {
            $this->search->lock();
        }
    }

    public function stopSearching(): void
    {
        $this->search = null;
    }

    public function left(): void
    {
        $selectedType = $this->selectedType();
        if (!$selectedType) {
            return;
        }

        $this->selectedTypeProp = (int) max([$this->selectedTypeProp - 1, 0]);
    }

    public function right(): void
    {
        $selectedType = $this->selectedType();
        if (!$selectedType) {
            return;
        }

        $this->selectedTypeProp = (int) min([$this->selectedTypeProp + 1, count($selectedType->getProperties()) - 1]);
    }

    public function toggleInformationType(): void
    {
        $this->informationType = $this->informationType->toggle();
    }

    public function zoom(?int $zoom): void
    {
        $this->zoom = $zoom;
    }

    public function renderWidgetAtZoom(int $zoom, Widget $widget): Widget|null
    {
        return ($this->zoom === null || $this->zoom === $zoom) ? $widget : null;
    }

    public function handle(Event $event): void
    {
        $this->search?->handle($event);

        if ($event instanceof Event\CodedKeyEvent) {
            match ($event->code) {
                KeyCode::Down => $this->down(),
                KeyCode::Up => $this->up(),
                KeyCode::Left => $this->left(),
                KeyCode::Right => $this->right(),
                KeyCode::Esc => $this->reset(),
                KeyCode::Enter => $this->lockSearching(),
                KeyCode::Tab => $this->toggleInformationType(),
                default => null,
            };
        }

        if ($event instanceof Event\CharKeyEvent) {
            match ($event->char) {
                '/' => $this->startSearching(),
                '0' => $this->zoom(null),
                '1' => $this->zoom(1),
                '2' => $this->zoom(2),
                '3' => $this->zoom(3),
                default => null,
            };

            // Reset selected type when searching on every key press:
            if ($this->search) {
                $this->selectedType = 0;
            }
        }
    }
}
