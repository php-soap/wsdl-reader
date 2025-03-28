<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Page;

use PhpTui\Term\Event;
use PhpTui\Term\KeyCode;
use PhpTui\Tui\Widget\Widget;
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Console\UI\Components\SearchState;
use Soap\WsdlReader\Console\UI\EventHandler;
use Soap\WsdlReader\Console\UI\UIState;
use function Psl\Math\max;
use function Psl\Math\min;
use function Psl\Vec\filter;
use function Psl\Vec\values;

final class MethodsPageState implements EventHandler
{
    public function __construct(
        public UIState $state,
        private int $selectedMethod = 0,
        private int $selectedMethodParam = 0,
        public ?SearchState $search = null,
        public InformationType $informationType = InformationType::Type,
        public ?int $zoom = null,
    ) {
    }

    public function selectedMethodIndex(): int
    {
        return $this->selectedMethod;
    }

    public function selectedMethod(): ?Method
    {
        return values($this->methods())[$this->selectedMethod] ?? null;
    }

    public function selectedMethodParamName(): ?string
    {
        $selectedMethod = $this->selectedMethod();
        if (!$selectedMethod) {
            return null;
        }

        $params = values($selectedMethod->getParameters());
        $param = $params[$this->selectedMethodParam] ?? null;

        return $param?->getName();
    }

    public function selectedMethodParamType(): ?XsdType
    {
        $selectedMethod = $this->selectedMethod();
        if (!$selectedMethod) {
            return null;
        }

        $params = values($selectedMethod->getParameters());
        if ($this->selectedMethodParam >= count($params)) {
            return $selectedMethod->getReturnType();
        }

        return $params[$this->selectedMethodParam]?->getType() ?? null;
    }

    /**
     * @return list<Method>
     */
    public function methods(): array
    {
        $query = (string) $this->search?->query;

        return \Psl\Vec\sort(
            filter(
                $this->state->metadata->getMethods(),
                static fn (Method $method) => !$query || mb_stripos($method->getName(), $query) !== false
            ),
            static fn (Method $a, Method $b) => $a->getName() <=> $b->getName()
        );
    }

    public function totalMethods(): int
    {
        return count($this->state->metadata->getMethods());
    }

    public function totalFilteredMethods(): int
    {
        return count($this->methods());
    }

    public function up(): void
    {
        $this->selectedMethod = (int) max([$this->selectedMethod-1, 0]);
        $this->selectedMethodParam = 0;
    }

    public function down(): void
    {
        $this->selectedMethod = (int) min([$this->selectedMethod + 1, count($this->methods()) - 1]);
        $this->selectedMethodParam = 0;
    }

    public function left(): void
    {
        $selectedType = $this->selectedMethod();
        if (!$selectedType) {
            return;
        }

        $this->selectedMethodParam = (int) max([$this->selectedMethodParam - 1, 0]);
    }

    public function right(): void
    {
        $selectedType = $this->selectedMethod();
        if (!$selectedType) {
            return;
        }

        $this->selectedMethodParam = (int) min([$this->selectedMethodParam + 1, count($selectedType->getParameters())]);
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

            // Reset selected method when searching on every key press:
            if ($this->search) {
                $this->selectedMethod = 0;
            }
        }
    }
}
