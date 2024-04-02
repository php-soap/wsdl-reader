--TEST--
SOAP XML Schema 1010: Anonymous sequence element container
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="a">
        <choice minOccurs="0">
            <element name="flag" type="boolean" />
            <sequence>
                <element name="mandatory" type="string" />
                <element minOccurs="0" name="optional" type="boolean" />
            </sequence>
        </choice>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:a {
    ?boolean $flag
    ?string $mandatory
    ?boolean $optional
  }
