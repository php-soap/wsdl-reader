--TEST--
SOAP XML Schema 14: simpleType/union
--INI--
precision=14
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="testType">
        <union memberTypes="string int float"/>
    </simpleType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType = (string|int|float)
