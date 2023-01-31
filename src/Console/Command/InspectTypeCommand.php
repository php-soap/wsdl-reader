<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Console\Command;

use Soap\Engine\Metadata\Model\Type;
use Soap\Wsdl\Console\Helper\ConfiguredLoader;
use Soap\WsdlReader\Formatter\LongTypeFormatter;
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

final class InspectTypeCommand extends Command
{
    public static function getDefaultName(): string
    {
        return 'inspect:type';
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setDescription('Inspects types from WSDL file.');
        $this->addArgument('wsdl', InputArgument::REQUIRED, 'Provide the URI of the WSDL you want to validate');
        $this->addArgument('type', InputArgument::REQUIRED, 'What WSDL type do you want to inspect?');
        $this->addOption('loader', 'l', InputOption::VALUE_REQUIRED, 'Customize the WSDL loader file that will be used');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $loader = ConfiguredLoader::createFromConfig($input->getOption('loader'));
        $wsdl = non_empty_string()->assert($input->getArgument('wsdl'));
        $typeName = $input->getArgument('type');

        $style->info('Loading "'.$wsdl.'"...');

        $wsdl = (new Wsdl1Reader($loader))($wsdl);
        $metadataProvider = new Wsdl1MetadataProvider($wsdl);
        $metadata = $metadataProvider->getMetadata();

        $detectedTypes = $metadata->getTypes()->filter(static fn (Type $type): bool => $type->getName() === $typeName);
        if (!$detectedTypes->count()) {
            $style->error('Unable to find type '.$typeName);
            return self::FAILURE;
        }

        $style->writeln(
            $detectedTypes->map(static fn (Type $type) => '  > '.(new LongTypeFormatter())($type))
        );

        return self::SUCCESS;
    }
}
