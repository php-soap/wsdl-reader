<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Console\Command;

use Soap\Engine\Metadata\Model\Method;
use Soap\Wsdl\Console\Helper\ConfiguredLoader;
use Soap\WsdlReader\Formatter\LongMethodFormatter;
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
        $this->addArgument('methods', InputArgument::IS_ARRAY, 'What WSDL method do you want to inspect?');
        $this->addOption('loader', 'l', InputOption::VALUE_REQUIRED, 'Customize the WSDL loader file that will be used');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $loader = ConfiguredLoader::createFromConfig($input->getOption('loader'));
        $wsdl = non_empty_string()->assert($input->getArgument('wsdl'));
        $methods = $input->getArgument('methods');
        $normalize = lowercase(...);
        $normalizedMethods = map($methods, static fn (string $method) => $normalize($method));

        $style->info('Loading "'.$wsdl.'"...');

        $wsdl = (new Wsdl1Reader($loader))($wsdl);
        $metadataProvider = new Wsdl1MetadataProvider($wsdl);
        $metadata = $metadataProvider->getMetadata();

        $detectedMethods = filter(
            $metadata->getMethods(),
            static fn (Method $methodInfo): bool => contains($normalizedMethods, $normalize($methodInfo->getName()))
        );

        if (!$detectedMethods) {
            $style->error('Unable to find methods '.join(', ', $methods));
            return self::FAILURE;
        }

        foreach ($detectedMethods as $detectedMethod) {
            $style->title($detectedMethod->getName());
            $style->writeln(['> '.(new LongMethodFormatter())($detectedMethod), '']);
            (new MetaTableFormatter($output))($detectedMethod->getMeta())->render();
        }

        return self::SUCCESS;
    }
}
