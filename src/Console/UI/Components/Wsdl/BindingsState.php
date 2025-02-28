<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components\Wsdl;

use PhpTui\Term\Event;
use PhpTui\Term\KeyCode;
use Soap\WsdlReader\Console\UI\Components\ScrollableTextAreaState;
use Soap\WsdlReader\Console\UI\EventHandler;
use Soap\WsdlReader\Console\UI\Page\WsdlPageState;

use Soap\WsdlReader\Model\Definitions\Binding;
use function Psl\Math\max;
use function Psl\Math\min;
use function Psl\Vec\filter;

final class BindingsState implements EventHandler
{
    public ScrollableTextAreaState $textAreaState;

    public function __construct(
        private WsdlPageState $pageState,
        private int $currentBinding = 0,
    ) {
        $this->trackTextAreaState();
    }

    public function trackTextAreaState(): ScrollableTextAreaState
    {
        return $this->textAreaState= ScrollableTextAreaState::json($this->currentBinding(), 'No binding found');
    }

    /**
     * @return list<Binding>
     */
    public function bindings(): array
    {
        $query = (string) $this->pageState->search?->query;

        return filter(
            $this->pageState->wsdl()->bindings->items,
            static fn (Binding $binding) => $query === '' || mb_stripos($binding->name, $query) !== false
        );
    }

    public function currentBindingIndex(): int
    {
        return $this->currentBinding;
    }

    public function currentBinding(): ?Binding
    {
        return $this->bindings()[$this->currentBinding] ?? null;
    }

    public function totalBindings(): int
    {
        return count($this->pageState->wsdl()->bindings->items);
    }

    public function totalFilteredBindings(): int
    {
        return count($this->bindings());
    }


    public function up(): void
    {
        $this->currentBinding = (int) max([0, $this->currentBinding - 1]);
        $this->trackTextAreaState();
    }

    public function down(): void
    {
        $this->currentBinding = (int) min([count($this->bindings()) - 1, $this->currentBinding + 1]);
        $this->trackTextAreaState();
    }

    public function handle(Event $event): void
    {
        $this->textAreaState->handle($event);

        if ($event instanceof Event\CodedKeyEvent) {
            match ($event->code) {
                KeyCode::Down => $this->down(),
                KeyCode::Up => $this->up(),
                default => null,
            };
        }
    }
}
