<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Metadata;

use Soap\WsdlReader\Metadata\Collection\MethodCollection;
use Soap\WsdlReader\Metadata\Collection\TypeCollection;

interface MetadataInterface
{
    public function getTypes(): TypeCollection;
    public function getMethods(): MethodCollection;
}
