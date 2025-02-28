<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Console\Command;

use PhpTui\Term\Actions;
use PhpTui\Term\ClearType;
use PhpTui\Term\Terminal;
use PhpTui\Tui\Bridge\PhpTerm\PhpTermBackend;
use PhpTui\Tui\Display\Display;
use PhpTui\Tui\DisplayBuilder;
use Psl\Ref;
use Soap\Wsdl\Console\Helper\ConfiguredLoader;
use Soap\Wsdl\Loader\CallbackLoader;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\WsdlLoader;
use Soap\WsdlReader\Console\UI\Components\LoadingWidget;
use Soap\WsdlReader\Console\UI\Layout;
use Soap\WsdlReader\Console\UI\UIState;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function Psl\Type\non_empty_string;

final class InspectUICommand extends Command
{
    public static function getDefaultName(): string
    {
        return 'inspect:ui';
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setDescription('Inspects WSDL file through a user interface.');
        $this->addArgument('wsdl', InputArgument::REQUIRED, 'Provide the URI of the WSDL you want to validate');
        $this->addOption('loader', 'l', InputOption::VALUE_REQUIRED, 'Customize the WSDL loader file that will be used');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $terminal = Terminal::new();
        $terminal->execute(Actions::alternateScreenEnable());
        $terminal->execute(Actions::enableMouseCapture());
        $terminal->execute(Actions::cursorHide());
        $terminal->enableRawMode();

        $display = DisplayBuilder::default(PhpTermBackend::new($terminal))->build();
        $state = $this->loadState($input, $display);

        try {
            while ($state->running) {
                while (null !== $event = $terminal->events()->next()) {
                    $state->handle($event);
                }

                $display->draw(Layout::create($state));

                usleep(50_000);
            }
        } finally {
            $terminal->disableRawMode();
            $terminal->execute(Actions::alternateScreenDisable());
            $terminal->execute(Actions::disableMouseCapture());
            $terminal->execute(Actions::cursorShow());
            $terminal->execute(Actions::clear(ClearType::All));
        }

        return self::SUCCESS;
    }

    private function loadState(InputInterface $input, Display $display): UIState
    {
        $wsdl = non_empty_string()->assert($input->getArgument('wsdl'));
        /** @var Ref<list<string>> $info */
        $info = new Ref(['Loading WSDL ...']);
        $display->draw(LoadingWidget::create($info->value));

        $loader = ConfiguredLoader::createFromConfig(
            $input->getOption('loader'),
            static fn (WsdlLoader $loader) => new FlatteningLoader(
                new CallbackLoader(static function (string $location) use ($loader, $display, $info): string {
                    $info->value[] = '> Loading '.$location.' ...';
                    $currentIndex = count($info->value) - 1;
                    $display->draw(LoadingWidget::create($info->value));

                    $result = $loader($location);

                    $info->value[$currentIndex] .= ' OK';
                    $display->draw(LoadingWidget::create($info->value));

                    return $result;
                })
            ),
        );

        return UIState::load($wsdl, $loader);
    }
}
