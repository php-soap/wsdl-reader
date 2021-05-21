<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Collection;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Soap\WsdlReader\Exception\MetadataException;
use Soap\WsdlReader\Metadata\Model\Method;
use function Psl\Vec\map;

class MethodCollection implements IteratorAggregate, Countable
{
    /**
     * @var Method[]
     */
    private array $methods;

    public function __construct(Method ...$methods)
    {
        $this->methods = $methods;
    }

    /**
     * @return ArrayIterator|Method[]
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->methods);
    }

    public function count(): int
    {
        return count($this->methods);
    }

    public function map(callable  $callback): array
    {
        return map($callback, $this->methods);
    }

    public function fetchOneByName(string $name): Method
    {
        foreach ($this->methods as $method) {
            if ($name === $method->getName()) {
                return $method;
            }
        }

        throw MetadataException::methodNotFound($name);
    }
}
