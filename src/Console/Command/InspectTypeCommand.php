<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Console\Command;

use Soap\Engine\Metadata\Model\Type;
use Soap\Wsdl\Console\Helper\ConfiguredLoader;
use Soap\WsdlReader\Formatter\LongTypeFormatter;
use Soap\WsdlReader\Formatter\MetaTableFormatter;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Psl\Iter\contains;
use function Psl\Str\Byte\lowercase;
use function Psl\Type\non_empty_string;
use function Psl\Vec\map;

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
        $this->addArgument('types', InputArgument::IS_ARRAY, 'What WSDL type do you want to inspect?');
        $this->addOption('loader', 'l', InputOption::VALUE_REQUIRED, 'Customize the WSDL loader file that will be used');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $loader = ConfiguredLoader::createFromConfig($input->getOption('loader'));
        $wsdl = non_empty_string()->assert($input->getArgument('wsdl'));
        $typeNames = $input->getArgument('types');
        $normalize = lowercase(...);
        $normalizedTypeNames = map($typeNames, static fn (string $type) => $normalize($type));

        $style->info('Loading "'.$wsdl.'"...');

        $wsdl = (new Wsdl1Reader($loader))($wsdl);
        $metadataProvider = new Wsdl1MetadataProvider($wsdl);
        $metadata = $metadataProvider->getMetadata();

        $detectedTypes = $metadata->getTypes()->filter(
            static fn (Type $type): bool => contains($normalizedTypeNames, $normalize($type->getName()))
        );
        if (!$detectedTypes->count()) {
            $style->error('Unable to find types '.join(', ', $typeNames));
            return self::FAILURE;
        }

        foreach ($detectedTypes as $detectedType) {
            $style->title($detectedType->getName());
            $style->writeln(['> '.(new LongTypeFormatter())($detectedType), '']);
            (new MetaTableFormatter($output))($detectedType->getXsdType()->getMeta())->render();
        }

        return self::SUCCESS;
    }
}
