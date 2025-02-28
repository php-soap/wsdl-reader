<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components;

use PhpTui\Term\Event;
use PhpTui\Term\KeyCode;
use PhpTui\Term\KeyModifiers;
use Soap\WsdlReader\Console\UI\EventHandler;

final class SearchState implements EventHandler
{
    public string $query = '';
    public bool $locked = false;

    public static function empty(): self
    {
        return new self();
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    public function unlock(): void
    {
        $this->locked = false;
    }

    public function handle(Event $event): void
    {
        if ($this->locked) {
            return;
        }

        if ($event instanceof Event\CharKeyEvent && $event->modifiers === KeyModifiers::NONE) {
            $this->query .= $event->char;
        }

        if ($event instanceof Event\CodedKeyEvent) {
            match ($event->code) {
                KeyCode::Backspace => $this->query = substr($this->query, 0, -1),
                default => null,
            };
        }
    }
}
