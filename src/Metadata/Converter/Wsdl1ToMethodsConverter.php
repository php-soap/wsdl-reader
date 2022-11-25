<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter;

use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\WsdlReader\Model\Wsdl1;

class Wsdl1ToMethodsConverter
{
    public function __invoke(Wsdl1 $wsdl): MethodCollection
    {
        $bindings = $wsdl->bindings->items;
        $operations = $wsdl->bindings->items;


        return new MethodCollection();
    }


    /*
    public function serviceReader(Document $wsdl): array
    {
        $services = new ServiceIterator($wsdl);
        $ports = iterator_to_array(new PortIterator($wsdl), true);
        $bindings = iterator_to_array(new BindingIterator($wsdl), true);
        $messages = iterator_to_array(new MessageIterator($wsdl), true);
        $namespaces = recursive_linked_namespaces($wsdl->map(document_element()));
        $parseQname = new QnameParser();

        foreach ($services as $service) {
            var_dump($service);
            [$bindingNamespace, $requiredBinding] = $parseQname($service['port']['binding']);
            $binding = $bindings[$requiredBinding] ?? null;
            [$portNamespace, $requiredPort] = $parseQname($binding['type']);
            $port = $ports[$requiredPort] ?? null;

            if (!$binding || !$port) {
                continue;
            }

            return [
                'service' => $service,
                'port' => $port,
                'binding' => $binding,
                'messages' => $messages,
                'namespaceMap' => $namespaces->reduce(
                    static fn (array $map, DOMNameSpaceNode $node): array
                    => merge($map, [$node->localName => $node->namespaceURI]),
                    []
                ),
            ];
        }

        throw new RuntimeException('Parsing WSDL: Couldn\'t bind to any service');
    }


    public function readMethods(Document $wsdl): MethodCollection
    {
        $service = $this->serviceReader->read($wsdl);

        return new MethodCollection(...array_values(array_map(
            fn (array $operation) => $this->parseMethod($service, $operation['name']),
            $service['binding']['operations']
        )));
    }

    private function parseMethod(array $service, string $operationName): Method
    {
        $portInfo = $service['port']['operations'][$operationName] ?? [];
        $inputInfo = $portInfo['input'];
        $outputInfo = $portInfo['output'];

        $filterMessageName = static fn (string $namespaced): string => (new QnameParser())($namespaced)[1];
        $inputMessage = $filterMessageName($inputInfo['message']);
        $outputMessage = $filterMessageName($outputInfo['message']);

        $messages = [
            $inputMessage => $service['messages'][$inputMessage] ?? [],
            $outputMessage => $service['messages'][$outputMessage] ?? [],
        ];

        return new Method(
            $operationName,
            new ParameterCollection(...$this->parseXsdTypesFromMessage($service, $messages[$inputMessage])),
            current($this->parseXsdTypesFromMessage($service, $messages[$outputMessage]))->getType()
        );
    }

    /**
     * @return array|Parameter[]
    private function parseXsdTypesFromMessage(array $service, array $message): array
    {
        $lookupNsUri = static fn (string $prefix): string => $service['namespaceMap'][$prefix] ?? '';

        return array_values(array_map(
            static function (array $param) use ($lookupNsUri): Parameter {
                [$elementNamespaceAlias, $elementName] = (new QnameParser())($param['element']);

                return new Parameter(
                    $elementName,
                    XsdType::guess($elementName)
                        ->withXmlNamespaceName($elementNamespaceAlias)
                        ->withXmlNamespace($lookupNsUri($elementNamespaceAlias))
                        ->withMeta(
                            [
                                'min' => 1,
                                'max' => 1,
                            ]
                        )
                );
            },
            $message['parts']
        ));
    }
     */
}
