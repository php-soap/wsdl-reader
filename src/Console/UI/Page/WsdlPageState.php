<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Page;

use PhpTui\Term\Event;
use PhpTui\Term\KeyCode;
use PhpTui\Tui\Widget\Widget;
use Soap\WsdlReader\Console\UI\Components\SearchState;
use Soap\WsdlReader\Console\UI\Components\Wsdl;
use Soap\WsdlReader\Console\UI\EventHandler;
use Soap\WsdlReader\Console\UI\UIState;
use Soap\WsdlReader\Model\Wsdl1;

final class WsdlPageState implements EventHandler
{
    public Wsdl\BindingsState $bindingsState;
    public Wsdl\MessagesState $messagesState;
    public Wsdl\NamespacesState $namespacesState;
    public Wsdl\PortTypesState $portTypesState;
    public Wsdl\ServicesState $servicesState;


    public function __construct(
        private UIState $state,
        public ?SearchState $search = null,
        public WsdlInfo $infoPart = WsdlInfo::Services,
    ) {
        $this->bindingsState = new Wsdl\BindingsState($this);
        $this->messagesState = new Wsdl\MessagesState($this);
        $this->namespacesState = new Wsdl\NamespacesState($this);
        $this->portTypesState = new Wsdl\PortTypesState($this);
        $this->servicesState = new Wsdl\ServicesState($this);
    }

    public function path(): string
    {
        return $this->state->wsdlPath;
    }

    public function wsdl(): Wsdl1
    {
        return $this->state->wsdl1;
    }

    public function buildInfoWidget(): Widget
    {
        return match($this->infoPart) {
            WsdlInfo::Services => Wsdl\ServicesWidget::create($this->servicesState),
            WsdlInfo::PortTypes => Wsdl\PortTypesWidget::create($this->portTypesState),
            WsdlInfo::Messages => Wsdl\MessagesWidget::create($this->messagesState),
            WsdlInfo::Namespaces => Wsdl\NamespacesWidget::create($this->namespacesState),
            WsdlInfo::Bindings => Wsdl\BindingsWidget::create($this->bindingsState),
        };
    }

    public function searching(): bool
    {
        return $this->search !== null && $this->search->locked === false;
    }

    public function reset(): void
    {
        $this->stopSearching();
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

    public function handle(Event $event): void
    {
        $this->search?->handle($event);

        if ($event instanceof Event\CodedKeyEvent) {
            match ($event->code) {
                KeyCode::Esc => $this->reset(),
                KeyCode::Enter => $this->lockSearching(),
                default => null,
            };
        }

        if ($event instanceof Event\CharKeyEvent) {
            match ($event->char) {
                '/' => $this->startSearching(),
                default => null,
            };

            foreach (WsdlInfo::cases() as $index => $infoPart) {
                if ($event->char === (string) ($index + 1)) {
                    $this->infoPart = $infoPart;
                }
            }
        }

        match ($this->infoPart) {
            WsdlInfo::Bindings => $this->bindingsState->handle($event),
            WsdlInfo::Messages => $this->messagesState->handle($event),
            WsdlInfo::Namespaces => $this->namespacesState->handle($event),
            WsdlInfo::PortTypes => $this->portTypesState->handle($event),
            WsdlInfo::Services => $this->servicesState->handle($event),
        };
    }
}
