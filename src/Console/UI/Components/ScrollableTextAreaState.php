<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components;

use PhpTui\Term\Event;
use PhpTui\Term\MouseEventKind;
use Soap\WsdlReader\Console\UI\EventHandler;

use function json_encode;
use function Psl\Math\max;
use function Psl\Math\min;

final class ScrollableTextAreaState implements EventHandler
{
    public function __construct(
        public string $value,
        public int $position = 0,
    ) {
    }

    public static function json(mixed $data, string $fallback): self
    {
        return new self(
            $data !== null ? json_encode($data, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES) : $fallback,
        );
    }

    public function scrollUp(): void
    {
        $this->position = (int) max([0, $this->position - 1]);
    }

    public function scrollDown(): void
    {
        $this->position = (int) min([$this->position + 1, count(explode("\n", $this->value))]);
    }

    public function handle(Event $event): void
    {
        if ($event instanceof Event\MouseEvent) {
            match ($event->kind) {
                MouseEventKind::ScrollUp => $this->scrollUp(),
                MouseEventKind::ScrollDown => $this->scrollDown(),
                default => null,
            };
        }
    }
}
