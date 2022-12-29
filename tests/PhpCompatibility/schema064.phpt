--TEST--
SOAP XML Schema 64: standard date/time types
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <sequence>
            <element name="dateTime" type="dateTime"/>
            <element name="time" type="time"/>
            <element name="date" type="date"/>
            <element name="gYearMonth" type="gYearMonth"/>
            <element name="gYear" type="gYear"/>
            <element name="gMonthDay" type="gMonthDay"/>
            <element name="gDay" type="gDay"/>
            <element name="gMonth" type="gMonth"/>
        </sequence>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    dateTime $dateTime
    time $time
    date $date
    gYearMonth $gYearMonth
    gYear $gYear
    gMonthDay $gMonthDay
    gDay $gDay
    gMonth $gMonth
  }
