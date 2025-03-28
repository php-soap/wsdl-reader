<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI;

use PhpTui\Term\Event;

interface EventHandler
{
    public function handle(Event $event): void;
}
