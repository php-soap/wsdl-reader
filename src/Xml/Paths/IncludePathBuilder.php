<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Xml\Paths;

use League\Uri\Uri;
use League\Uri\UriModifier;
use League\Uri\UriResolver;

final class IncludePathBuilder
{
    public static function build(string $relativePath, string $fromFile): string
    {
        return UriModifier::removeEmptySegments(
            UriModifier::removeDotSegments(
                UriResolver::resolve(
                    Uri::createFromString($relativePath),
                    Uri::createFromString($fromFile)
                )
            )
        )->__toString();
    }
}
