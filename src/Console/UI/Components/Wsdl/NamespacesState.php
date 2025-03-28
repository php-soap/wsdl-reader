<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components\Wsdl;

use PhpTui\Term\Event;
use PhpTui\Term\KeyCode;
use Soap\WsdlReader\Console\UI\EventHandler;
use Soap\WsdlReader\Console\UI\Page\WsdlPageState;

use function Psl\Dict\filter_with_key;
use function Psl\Math\max;
use function Psl\Math\min;

final class NamespacesState implements EventHandler
{
    public function __construct(
        private WsdlPageState $pageState,
        private int $currentNamespace = 0,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function namespaces(): array
    {
        $query = (string) $this->pageState->search?->query;

        return filter_with_key(
            $this->pageState->wsdl()->namespaces->namespaceToNameMap,
            static fn (string $namespace, string $prefix) => $query === ''
                || mb_stripos($namespace, $query) !== false
                || mb_stripos($prefix, $query) !== false
        );
    }

    public function currentNamespaceIndex(): int
    {
        return $this->currentNamespace;
    }

    public function totalNamespaces(): int
    {
        return count($this->pageState->wsdl()->namespaces->namespaceToNameMap);
    }

    public function totalFilteredNamespaces(): int
    {
        return count($this->namespaces());
    }


    public function up(): void
    {
        $this->currentNamespace = (int) max([0, $this->currentNamespace - 1]);
    }

    public function down(): void
    {
        $this->currentNamespace = (int) min([count($this->namespaces()) - 1, $this->currentNamespace + 1]);
    }

    public function handle(Event $event): void
    {
        if ($event instanceof Event\CodedKeyEvent) {
            match ($event->code) {
                KeyCode::Down => $this->down(),
                KeyCode::Up => $this->up(),
                default => null,
            };
        }
    }
}
