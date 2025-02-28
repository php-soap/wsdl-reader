<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components;

use PhpTui\Tui\Extension\Core\Widget\List\ListItem;
use PhpTui\Tui\Extension\Core\Widget\ListWidget;
use PhpTui\Tui\Style\Style;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Widget;
use function Psl\Vec\map;

final readonly class LoadingWidget
{
    /**
     * @param list<string> $messages
     */
    public static function create(array $messages): Widget
    {
        return ListWidget::default()
            ->items(
                ...map(
                    $messages,
                    static fn (string $message) => ListItem::new(Text::fromString($message))
                )
            )
            ->select(count($messages) - 1)
            ->highlightStyle(Style::default()->white()->onBlue());
    }
}
