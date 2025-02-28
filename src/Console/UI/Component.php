<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI;

use PhpTui\Tui\Widget\Widget;

interface Component extends EventHandler
{
    public function build(): Widget;
}
