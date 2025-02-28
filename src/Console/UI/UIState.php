<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI;

use PhpTui\Term\Event;
use PhpTui\Term\KeyModifiers;
use Soap\Engine\Metadata\Metadata;
use Soap\Wsdl\Loader\WsdlLoader;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Model\Wsdl1;
use Soap\WsdlReader\Wsdl1Reader;

final class UIState implements EventHandler
{
    public string $wsdlPath;
    public Wsdl1 $wsdl1;
    public Metadata $metadata;
    public Page $currentPage;
    /**
     * @var array<class-string<Page>, Page>
     */
    public array $availablePages;
    public bool $running = true;

    private function __construct(
        string $wsdlPath,
        Wsdl1 $wsdl1,
        Metadata $metadata,
    ) {
        $this->wsdlPath = $wsdlPath;
        $this->wsdl1 = $wsdl1;
        $this->metadata = $metadata;
        $this->availablePages = [
            Page\MethodsPage::class => new Page\MethodsPage($this),
            Page\TypesPage::class => new Page\TypesPage($this),
            Page\WsdlPage::class => new Page\WsdlPage($this),
        ];
        $this->currentPage = $this->availablePages[Page\MethodsPage::class];
    }

    /**
     * @param non-empty-string $wsdlPath
     */
    public static function load(
        string $wsdlPath,
        WsdlLoader $loader,
    ): self {
        $wsdl = (new Wsdl1Reader($loader))($wsdlPath);
        $metadataProvider = new Wsdl1MetadataProvider($wsdl);
        $metadata = $metadataProvider->getMetadata();

        return new self(
            $wsdlPath,
            $wsdl,
            $metadata
        );
    }

    public function handle(Event $event): void
    {
        $this->currentPage->handle($event);

        if ($event instanceof Event\CharKeyEvent && !$this->currentPage->isBlockingParentEvents()) {
            foreach ($this->availablePages as $page) {
                if ($event->char === $page->navigationChar()) {
                    $this->currentPage = $page;
                    return;
                }
            }

            match ($event->char) {
                'q' => $this->running = false,
                default => null,
            };
        }


        // CTRL+C always exits !
        if ($event instanceof Event\CharKeyEvent && $event->char === 'c' && ($event->modifiers & KeyModifiers::CONTROL)) {
            $this->running = false;
        }
    }
}
