--TEST--
SOAP XML Schema 4: simpleType/restriction (reference to undefined type)
--INI--
precision=14
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="testType">
        <restriction base="tns:testType2"/>
    </simpleType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECT--
FATAL (GoetasWebservices\XML\XSDReader\Exception\TypeException):Can't find type named {http://test-uri/}#testType2, at line 18 in some.wsdl
