<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Xml\Validator;

use DOMDocument;
use VeeWee\Xml\Dom\Document;
use VeeWee\Xml\ErrorHandling\Issue\IssueCollection;
use function VeeWee\Xml\Dom\Validator\xsd_validator;

class WsdlSyntaxValidator //implements Validator
{
    private string $xsd;

    public function __construct(?string $xsd = null)
    {
        $this->xsd = $xsd ?: dirname(__DIR__, 3).'/validators/wsdl.xsd';
    }

    public function __invoke(DOMDocument $document): IssueCollection
    {
        return Document::fromUnsafeDocument($document)->validate(xsd_validator($this->xsd));
    }
}
