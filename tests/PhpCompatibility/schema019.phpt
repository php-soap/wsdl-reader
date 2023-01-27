--TEST--
SOAP XML Schema 19: union with list
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="testType">
        <union>
            <simpleType>
                <restriction base="float"/>
            </simpleType>
            <simpleType>
                <list itemType="int"/>
            </simpleType>
        </union>
    </simpleType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType = (float|list<int>)
