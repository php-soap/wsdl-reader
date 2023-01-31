<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Console\Command;

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Type;
use Soap\Wsdl\Console\Helper\ConfiguredLoader;
use Soap\WsdlReader\Formatter\ShortMethodFormatter;
use Soap\WsdlReader\Formatter\ShortTypeFormatter;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Psl\Type\non_empty_string;

final class InspectCommand extends Command
{
    public static function getDefaultName(): string
    {
        return 'inspect';
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setDescription('Inspects WSDL file.');
        $this->addArgument('wsdl', InputArgument::REQUIRED, 'Provide the URI of the WSDL you want to validate');
        $this->addOption('loader', 'l', InputOption::VALUE_REQUIRED, 'Customize the WSDL loader file that will be used');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $loader = ConfiguredLoader::createFromConfig($input->getOption('loader'));
        $wsdl = non_empty_string()->assert($input->getArgument('wsdl'));

        $style->info('Loading "'.$wsdl.'"...');

        $wsdl = (new Wsdl1Reader($loader))($wsdl);
        $metadataProvider = new Wsdl1MetadataProvider($wsdl);
        $metadata = $metadataProvider->getMetadata();

        $style->info('Methods:');
        $style->writeln($metadata->getMethods()->map(static fn (Method $method) => '  > '.(new ShortMethodFormatter())($method)));

        $style->info('Types:');
        $style->writeln($metadata->getTypes()->map(static fn (Type $type) => '  > '.(new ShortTypeFormatter())($type)));

        return self::SUCCESS;
    }
}
