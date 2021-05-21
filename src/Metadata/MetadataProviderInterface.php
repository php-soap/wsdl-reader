<?php

declare( strict_types=1 );

namespace Soap\WsdlReader\Metadata;

interface MetadataProviderInterface
{
    public function getMetadata(): MetadataInterface;
}
