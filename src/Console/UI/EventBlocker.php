<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI;

interface EventBlocker
{
    public function isBlockingParentEvents(): bool;
}
