<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI;

use PhpTui\Tui\Text\Line;

interface Page extends Component, EventBlocker
{
    /**
     * @return Line[]
     */
    public function help(): array;

    public function title(): string;

    public function navigationChar(): string;
}
