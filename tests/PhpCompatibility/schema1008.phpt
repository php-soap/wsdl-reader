--TEST--
SOAP XML Schema 66: Required Attribute
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <attribute name="optional" type="string" use="optional" />
        <attribute name="required" type="string" use="required" />
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    @?string $optional
    @string $required
  }
