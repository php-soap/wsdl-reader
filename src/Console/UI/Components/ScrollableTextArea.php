<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Components;

use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Widget\Widget;

final class ScrollableTextArea
{
    public static function create(ScrollableTextAreaState $state): Widget
    {
        $paragraph =  ParagraphWidget::fromString($state->value);
        $paragraph->scroll = [$state->position, 0];

        return $paragraph;
    }
}
