--TEST--
SOAP XML Schema 32: Structure (choice)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <choice>
            <element name="int" type="int"/>
            <element name="str" type="string"/>
        </choice>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    ?int $int
    ?string $str
  }
