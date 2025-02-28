<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components\Wsdl;

use PhpTui\Term\Event;
use PhpTui\Term\KeyCode;
use Soap\WsdlReader\Console\UI\Components\ScrollableTextAreaState;
use Soap\WsdlReader\Console\UI\EventHandler;
use Soap\WsdlReader\Console\UI\Page\WsdlPageState;

use Soap\WsdlReader\Model\Definitions\Service;
use function Psl\Math\max;
use function Psl\Math\min;
use function Psl\Vec\filter;

final class ServicesState implements EventHandler
{
    public ScrollableTextAreaState $textAreaState;

    public function __construct(
        private WsdlPageState $pageState,
        private int $currentService = 0,
    ) {
        $this->trackTextAreaState();
    }

    public function trackTextAreaState(): ScrollableTextAreaState
    {
        return $this->textAreaState= ScrollableTextAreaState::json($this->currentService(), 'No service found');
    }

    /**
     * @return list<Service>
     */
    public function services(): array
    {
        $query = (string) $this->pageState->search?->query;

        return filter(
            $this->pageState->wsdl()->services->items,
            static fn (Service $service) => $query === '' || mb_stripos($service->name, $query) !== false
        );
    }

    public function currentServiceIndex(): int
    {
        return $this->currentService;
    }

    public function currentService(): ?Service
    {
        return $this->services()[$this->currentService] ?? null;
    }

    public function totalServices(): int
    {
        return count($this->pageState->wsdl()->services->items);
    }

    public function totalFilteredServices(): int
    {
        return count($this->services());
    }


    public function up(): void
    {
        $this->currentService = (int) max([0, $this->currentService - 1]);
        $this->trackTextAreaState();
    }

    public function down(): void
    {
        $this->currentService = (int) min([count($this->services()) - 1, $this->currentService + 1]);
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
