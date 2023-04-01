<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Console;

use Soap\Wsdl\Console\Configurator;
use Symfony\Component\Console\Application;

final class WsdlReaderConfigurator implements Configurator
{
    public static function configure(Application $application): void
    {
        $application->addCommands([
            new Command\InspectCommand(),
            new Command\InspectMethodCommand(),
            new Command\InspectTypeCommand(),
        ]);
    }
}
