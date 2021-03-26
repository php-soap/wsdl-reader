#!/usr/bin/env php
<?php

use Soap\WsdlReader\Loader\LocalFileLoader;
use Soap\WsdlReader\Xml\Parser;
use Soap\WsdlReader\Xml\Xpath\XpathProvider;
use VeeWee\Xml\Dom\Document;
use function \Psl\Str\join;
use function Psl\Vec\filter;
use function Psl\Vec\map;
use function VeeWee\Xml\Dom\Validator\xsd_validator;

require_once dirname(__DIR__).'/vendor/autoload.php';
$validators = dirname(__DIR__).'/validators';

// TODO : how to get this in veewee/xml ....
libxml_set_external_entity_loader(
    static fn (?string $public, string $system, array $context): string
        => match ($system) {
            'http://www.w3.org/2001/xml.xsd' => $validators.'/xml.xsd',
            default => $system
        }
);

(static function (array $argv) use ($validators) {
    if (!$file = $argv[1] ?? null) {
        throw new InvalidArgumentException('Expected wsdl file as first argument');
    }

    $parser = new Parser(new LocalFileLoader());
    $wsdl = $parser->parse($file);

    echo "Full WSDL:".PHP_EOL;
    echo $wsdl->toXmlString().PHP_EOL.PHP_EOL;

    echo "Validating WSDL:".PHP_EOL;
    $issues = $wsdl->validate(xsd_validator($validators.'/wsdl.xsd'));
    echo ($issues->toString() ?: '🟢 ALL GOOD').PHP_EOL.PHP_EOL;

    echo "Validating Schemas".PHP_EOL;
    echo (join(
        filter(map(
            XpathProvider::provide($wsdl)->query('//xsd:schema'),
            static fn (DOMElement $schema): string
                => Document::fromXmlNode($schema)
                    ->validate(xsd_validator($validators.'/XMLSchema.xsd'))
                    ->toString()
        )),
        PHP_EOL
    ) ?: '🟢 ALL GOOD').PHP_EOL.PHP_EOL;

})($argv);
