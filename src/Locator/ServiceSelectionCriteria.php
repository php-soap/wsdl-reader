<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Locator;

use Soap\WsdlReader\Model\Definitions\SoapVersion;

final class ServiceSelectionCriteria
{
    public ?SoapVersion $preferredSoapVersion;
    public bool $allowHttpPorts;
    public ?string $serviceName = null;
    public ?string $portName = null;

    public function __construct()
    {
        $this->preferredSoapVersion = null;
        $this->allowHttpPorts = true;
        $this->serviceName = null;
        $this->portName = null;
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

    public function withServiceName(?string $serviceName = null): self
    {
        $new = clone $this;
        $new->serviceName = $serviceName;

        return $new;
    }

    public function withPortName(?string $portName = null): self
    {
        $new = clone $this;
        $new->portName = $portName;

        return $new;
    }
}
