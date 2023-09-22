--TEST--
SOAP XML Schema 12: simpleType/list (inline type) (as array)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="testType">
        <list>
            <simpleType>
                <restriction base="int"/>
            </simpleType>
        </list>
    </simpleType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType = (list<int>)
