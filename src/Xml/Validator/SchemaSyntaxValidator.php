<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Xml\Validator;

use DOMDocument;
use DOMElement;
use Soap\WsdlReader\Xml\Xpath\XpathProvider;
use VeeWee\Xml\Dom\Document;
use VeeWee\Xml\ErrorHandling\Issue\IssueCollection;
use function Psl\Iter\reduce;
use function VeeWee\Xml\Dom\Validator\xsd_validator;

class SchemaSyntaxValidator // implements Validator
{
    private string $xsd;

    public function __construct(?string $xsd = null)
    {
        $this->xsd = $xsd ?: dirname(__DIR__, 3).'/validators/XMLSchema.xsd';
    }

    public function __invoke(DOMDocument $document): IssueCollection
    {
        $xpath = XpathProvider::provide(Document::fromUnsafeDocument($document));

        return reduce(
            $xpath->query('//xsd:schema'),
            fn (IssueCollection $issues, DOMElement $schema) => new IssueCollection(
                ...$issues,
                ...Document::fromXmlNode($schema)->validate(xsd_validator($this->xsd))
            ),
            new IssueCollection()
        );
    }
}
