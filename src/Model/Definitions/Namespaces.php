<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Psl\Option\Option;
use function Psl\Option\from_nullable;

final class Namespaces
{
    /**
     * @param array<string, string> $nameToNamespaceMap
     * @param array<string, string> $namespaceToNameMap
     */
    public function __construct(
        public readonly array $nameToNamespaceMap,
        public readonly array $namespaceToNameMap,
    ) {
    }

    /**
     * @return Option<string>
     */
    public function lookupNameFromNamespace(string $name): Option
    {
        return from_nullable($this->namespaceToNameMap[$name] ?? null);
    }
}
