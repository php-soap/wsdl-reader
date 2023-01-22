<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\LogicException;

final class AppFactory
{
    /**
     * @throws LogicException
     */
    public static function create(): Application
    {
        $app = new Application('wsdl-reader', '1.0.0');
        $app->addCommands([
            new Command\InspectCommand(),
            new Command\InspectMethodCommand(),
            new Command\InspectTypeCommand(),
        ]);

        return $app;
    }
}
