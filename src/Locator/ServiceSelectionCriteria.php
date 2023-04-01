<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Locator;

use Soap\WsdlReader\Model\Definitions\SoapVersion;

final class ServiceSelectionCriteria
{
    public ?SoapVersion $preferredSoapVersion;
    public bool $allowHttpPorts;

    public function __construct()
    {
        $this->preferredSoapVersion = null;
        $this->allowHttpPorts = true;
    }

    public static function defaults(): self
    {
        return (new self());
    }

    public function withAllowHttpPorts(bool $allowHttp = true): self
    {
        $new = clone $this;
        $new->allowHttpPorts = $allowHttp;

        return $new;
    }

    public function withPreferredSoapVersion(?SoapVersion $preferredSoapVersion = null): self
    {
        $new = clone $this;
        $new->preferredSoapVersion = $preferredSoapVersion;

        return $new;
    }
}
