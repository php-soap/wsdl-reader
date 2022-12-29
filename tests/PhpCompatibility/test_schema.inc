<?php

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Type;
use Soap\Wsdl\Loader\CallbackLoader;
use Soap\WsdlReader\Formatter\LongTypeFormatter;
use Soap\WsdlReader\Formatter\MethodFormatter;
use Soap\WsdlReader\Formatter\ShortTypeFormatter;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;

require_once __DIR__.'/../bootstrap.php';

function test_schema($schema, $type, $style="rpc",$use="encoded", $attributeFormDefault='')
{
  $wsdl = <<<EOF
  <definitions name="InteropTest"
      xmlns:xsd="http://www.w3.org/2001/XMLSchema"
      xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
      xmlns:tns="http://test-uri/"
      xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
      xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
      xmlns="http://schemas.xmlsoap.org/wsdl/"
      targetNamespace="http://test-uri/"
      >
    <types>
    <schema xmlns="http://www.w3.org/2001/XMLSchema" targetNamespace="http://test-uri/" $attributeFormDefault>
     <xsd:import namespace="http://schemas.xmlsoap.org/soap/encoding/" /><!-- SOAP 1.1-->
     <xsd:import namespace="http://www.w3.org/2003/05/soap-encoding" /><!-- SOAP 1.2 - 2003-05 -->
     <xsd:import namespace="http://xml.apache.org/xml-soap" />
     <xsd:import namespace="http://schemas.xmlsoap.org/wsdl/" />
      $schema
    </schema>
    </types>
    <message name="testMessage">
      <part name="testParam" $type/>
    </message>
      <portType name="testPortType">
          <operation name="test">
              <input message="testMessage"/>
          </operation>
      </portType>
      <binding name="testBinding" type="testPortType">
          <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
          <operation name="test">
              <soap:operation soapAction="#test" style="$style"/>
              <input>
                  <soap:body use="$use" namespace="http://test-uri/" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
              </input>
          </operation>
      </binding>
      <service name="testService">
     <port name="testPort" binding="tns:testBinding">
       <soap:address location="test://" />
     </port>
   </service>
  </definitions>
  EOF;

  try {
      $wsdl = (new Wsdl1Reader(new CallbackLoader(fn() => $wsdl)))('some.wsdl');
      $metadataProvider = new Wsdl1MetadataProvider($wsdl);
      $metadata = $metadataProvider->getMetadata();

      echo "Methods:" . PHP_EOL;
      echo implode(PHP_EOL, $metadata->getMethods()->map(fn(Method $method) => '  > ' . (new MethodFormatter())($method)));
      echo PHP_EOL . PHP_EOL;

      echo "Types:" . PHP_EOL;
      echo implode(PHP_EOL, $metadata->getTypes()->map(fn(Type $type) => '  > ' . (new LongTypeFormatter())($type)));
      echo PHP_EOL . PHP_EOL;
  } catch (\Exception $exception) {
      echo "FATAL (".get_class($exception).'):'. $exception->getMessage();
  }
}
