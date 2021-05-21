<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Collection;

use Soap\WsdlReader\Metadata\Model\Parameter;

class ParameterCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Parameter[]
     */
    private $parameters;

    public function __construct(Parameter ...$parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return \ArrayIterator|Parameter[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->parameters);
    }

    public function count(): int
    {
        return count($this->parameters);
    }

    public function map(callable  $callback): array
    {
        return array_map($callback, $this->parameters);
    }

    public function mapNames(): array
    {
        return $this->map(static function (Parameter $parameter): string {
            return $parameter->getName();
        });
    }

    public function unique(): self
    {
        return new ParameterCollection(...array_values(
            array_combine(
                $this->mapNames(),
                $this->parameters
            )
        ));
    }
}
