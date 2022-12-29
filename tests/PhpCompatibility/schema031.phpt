--TEST--
SOAP XML Schema 31: Structure (all)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <all>
            <element name="int" type="int"/>
            <element name="str" type="string"/>
        </all>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    int $int
    string $str
  }
