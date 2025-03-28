<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components\Wsdl;

use PhpTui\Term\Event;
use PhpTui\Term\KeyCode;
use Soap\WsdlReader\Console\UI\Components\ScrollableTextAreaState;
use Soap\WsdlReader\Console\UI\EventHandler;
use Soap\WsdlReader\Console\UI\Page\WsdlPageState;

use Soap\WsdlReader\Model\Definitions\PortType;
use function Psl\Math\max;
use function Psl\Math\min;
use function Psl\Vec\filter;

final class PortTypesState implements EventHandler
{
    public ScrollableTextAreaState $textAreaState;

    public function __construct(
        private WsdlPageState $pageState,
        private int $currentPortType = 0,
    ) {
        $this->trackTextAreaState();
    }

    public function trackTextAreaState(): ScrollableTextAreaState
    {
        return $this->textAreaState= ScrollableTextAreaState::json($this->currentPortType(), 'No port type found');
    }

    /**
     * @return list<PortType>
     */
    public function portTypes(): array
    {
        $query = (string) $this->pageState->search?->query;

        return filter(
            $this->pageState->wsdl()->portTypes->items,
            static fn (PortType $portType) => $query === '' || mb_stripos($portType->name, $query) !== false
        );
    }

    public function currentPortTypeIndex(): int
    {
        return $this->currentPortType;
    }

    public function currentPortType(): ?PortType
    {
        return $this->portTypes()[$this->currentPortType] ?? null;
    }

    public function totalPortTypes(): int
    {
        return count($this->pageState->wsdl()->portTypes->items);
    }

    public function totalFilteredPortTypes(): int
    {
        return count($this->portTypes());
    }


    public function up(): void
    {
        $this->currentPortType = (int) max([0, $this->currentPortType - 1]);
        $this->trackTextAreaState();
    }

    public function down(): void
    {
        $this->currentPortType = (int) min([count($this->portTypes()) - 1, $this->currentPortType + 1]);
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
