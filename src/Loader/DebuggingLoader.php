<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Loader;

use Soap\Wsdl\Loader\WsdlLoader;

class DebuggingLoader implements WsdlLoader
{
    public function __construct(
        private WsdlLoader $loader
    ){
    }

    public function __invoke(string $location): string
    {
        dump('Loading: '. $location);
        return ($this->loader)($location);
    }
}
