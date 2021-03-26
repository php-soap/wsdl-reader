<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Loader;

interface Loader
{
    /**
     * A loader can load a URI and return its contents.
     */
    public function load(string $location): string;
}
