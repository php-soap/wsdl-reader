--TEST--
SOAP XML Schema 85: Extension of complex type (elements order)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType2">
        <sequence>
            <element name="int" type="int"/>
        </sequence>
    </complexType>
    <complexType name="testType">
        <complexContent>
            <extension base="tns:testType2">
                <sequence>
                    <element name="int2" type="int"/>
                </sequence>
            </extension>
        </complexContent>
    </complexType>
EOF;
class A {
  public $int = 1;
}

class B extends A {
  public $int2 = 2;
}


test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType2 {
    int $int
  }
  > http://test-uri/:testType extends testType2 {
    int $int2
  }
