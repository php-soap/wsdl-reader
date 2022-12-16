<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods;

use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\WsdlReader\Model\Definitions\SoapVersion;

class MethodsConverterContext
{
    private ?SoapVersion $preferredSoapVersion = null;

    private function __construct(
        public readonly TypeCollection $types,
    ){
    }

    public static function defaults(TypeCollection $types): self
    {
        return new self($types, null);
    }

    public static function soap11(TypeCollection $types): self
    {
        return self::defaults($types)->withPreferredSoapVersion(SoapVersion::SOAP_11);
    }

    public static function soap12(TypeCollection $types): self
    {
        return self::defaults($types)->withPreferredSoapVersion(SoapVersion::SOAP_12);
    }

    public function withPreferredSoapVersion(SoapVersion $soapVersion): self
    {
        $new = clone $this;
        $new->preferredSoapVersion = $soapVersion;

        return $new;
    }

    public function preferredSoapVersion(): ?SoapVersion
    {
        return $this->preferredSoapVersion;
    }
}
