<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Predicate;

use Soap\Engine\Metadata\Model\XsdType;

final class IsOfType
{
    /**
     * @param non-empty-string $namespace
     * @param non-empty-string $name
     */
    public function __construct(
        private readonly string $namespace,
        private readonly string $name
    ) {
    }

    public function __invoke(XsdType $type): bool
    {
        $normalize = mb_strtolower(...);
        $expectedName = $normalize($this->name);
        $expectedNamespace = $normalize($this->namespace);

        if ($normalize($type->getXmlTypeName()) === $expectedName && $normalize($type->getXmlNamespace()) === $expectedNamespace) {
            return true;
        }

        $extends = $type->getMeta()
            ->extends()
            ->filter(static fn (array $extends): bool => $normalize($extends['type']) === $expectedName
                && $normalize($extends['namespace']) === $expectedNamespace);
        if ($extends->isSome()) {
            return true;
        }

        return false;
    }
}
