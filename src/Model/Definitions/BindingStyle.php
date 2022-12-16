<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

enum BindingStyle: string
{
    case DOCUMENT = 'document';
    case RPC = 'RPC';
}
