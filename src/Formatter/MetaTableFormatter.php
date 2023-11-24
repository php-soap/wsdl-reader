<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Formatter;

use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\map;

final class MetaTableFormatter
{
    public function __construct(
        private OutputInterface $output
    ) {
    }

    public function __invoke(object $object): Table
    {
        $table = new Table($this->output);
        $table
            ->setHeaders(['Meta', 'Value'])
            ->setRows($this->buildKeyPairs($object));

        return $table;
    }

    /**
     * @return list<array{0: non-empty-string, 1: string}>
     */
    private function buildKeyPairs(object $object): array
    {
        $rc = new ReflectionClass($object);

        return filter_nulls(
            map(
                $rc->getProperties(),
                /** @return array{0: non-empty-string, 1: string}|null */
                function (ReflectionProperty $prop) use ($object): ?array {
                    $value = $this->tryStringifyValue($prop->getValue($object));
                    if ($value === null) {
                        return null;
                    }

                    return [$prop->getName(), $value];
                }
            )
        );
    }

    private function tryStringifyValue(mixed $value): ?string
    {
        try {
            return match (true) {
                is_array($value) => json_encode($value, JSON_PRETTY_PRINT),
                is_bool($value) => $value ? 'true' : 'false',
                is_scalar($value) => (string)$value,
                default => null,
            };
        } catch (Throwable) {
            return null;
        }
    }
}
