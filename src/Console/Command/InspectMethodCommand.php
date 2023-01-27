<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Console\Command;

use Soap\Engine\Metadata\Model\Method;
use Soap\Wsdl\Console\Helper\ConfiguredLoader;
use Soap\WsdlReader\Formatter\LongMethodFormatter;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Psl\Vec\filter;
use function Psl\Vec\map;

final class InspectMethodCommand extends Command
{
    public static function getDefaultName(): string
    {
        return 'inspect:method';
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setDescription('Inspects a method of a WSDL file.');
        $this->addArgument('wsdl', InputArgument::REQUIRED, 'Provide the URI of the WSDL you want to validate');
        $this->addArgument('method', InputArgument::REQUIRED, 'What WSDL method do you want to inspect?');
        $this->addOption('loader', 'l', InputOption::VALUE_REQUIRED, 'Customize the WSDL loader file that will be used');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $loader = ConfiguredLoader::createFromConfig($input->getOption('loader'));
        $wsdl = $input->getArgument('wsdl');
        $method = $input->getArgument('method');

        $style->info('Loading "'.$wsdl.'"...');

        $wsdl = (new Wsdl1Reader($loader))($wsdl);
        $metadataProvider = new Wsdl1MetadataProvider($wsdl);
        $metadata = $metadataProvider->getMetadata();

        $detectedMethods = filter($metadata->getMethods(), static fn (Method $methodInfo): bool => $methodInfo->getName() === $method);
        if (!$detectedMethods) {
            $style->error('Unable to find method '.$method);
            return self::FAILURE;
        }

        $style->info('Methods:');
        $style->writeln(
            map(
                $detectedMethods,
                static fn (Method $method) => '  > '.(new LongMethodFormatter())($method)
            )
        );

        return self::SUCCESS;
    }
}
