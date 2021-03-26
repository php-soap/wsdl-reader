<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Loader;

use function Psl\Filesystem\read_file;

final class LocalFileLoader implements Loader
{
    public function load(string $location): string
    {
        return read_file($location);
    }
}
