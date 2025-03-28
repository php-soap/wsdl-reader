<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components\Wsdl;

use PhpTui\Term\Event;
use PhpTui\Term\KeyCode;
use Soap\WsdlReader\Console\UI\Components\ScrollableTextAreaState;
use Soap\WsdlReader\Console\UI\EventHandler;
use Soap\WsdlReader\Console\UI\Page\WsdlPageState;

use Soap\WsdlReader\Model\Definitions\Message;
use function Psl\Math\max;
use function Psl\Math\min;
use function Psl\Vec\filter;

final class MessagesState implements EventHandler
{
    public ScrollableTextAreaState $textAreaState;

    public function __construct(
        private WsdlPageState $pageState,
        private int $currentMessage = 0,
    ) {
        $this->trackTextAreaState();
    }

    public function trackTextAreaState(): ScrollableTextAreaState
    {
        return $this->textAreaState= ScrollableTextAreaState::json($this->currentMessage(), 'No message found');
    }

    /**
     * @return list<Message>
     */
    public function messages(): array
    {
        $query = (string) $this->pageState->search?->query;

        return filter(
            $this->pageState->wsdl()->messages->items,
            static fn (Message $message) => $query === '' || mb_stripos($message->name, $query) !== false
        );
    }

    public function currentMessageIndex(): int
    {
        return $this->currentMessage;
    }

    public function currentMessage(): ?Message
    {
        return $this->messages()[$this->currentMessage] ?? null;
    }

    public function totalMessages(): int
    {
        return count($this->pageState->wsdl()->messages->items);
    }

    public function totalFilteredMessages(): int
    {
        return count($this->messages());
    }


    public function up(): void
    {
        $this->currentMessage = (int) max([0, $this->currentMessage - 1]);
        $this->trackTextAreaState();
    }

    public function down(): void
    {
        $this->currentMessage = (int) min([count($this->messages()) - 1, $this->currentMessage + 1]);
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
