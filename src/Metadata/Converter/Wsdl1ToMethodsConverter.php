<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter;

use Psl\Option\Option;
use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\ParameterCollection;
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Parameter;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Locator\Wsdl1SelectedServiceLocator;
use Soap\WsdlReader\Metadata\Converter\Methods\MethodsConverterContext;
use Soap\WsdlReader\Model\Definitions\BindingOperation;
use Soap\WsdlReader\Model\Definitions\Message;
use Soap\WsdlReader\Model\Definitions\Operation;
use Soap\WsdlReader\Model\Definitions\Part;
use Soap\WsdlReader\Model\Service\Wsdl1SelectedService;
use Soap\WsdlReader\Model\Wsdl1;
use Soap\WsdlReader\Parser\Xml\QnameParser;
use Soap\WsdlReader\Todo\OptionsHelper;
use function Psl\Dict\pull;
use function Psl\Iter\first;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\map;
use function Psl\Vec\map_with_key;

/**
 * TODO : split in small testable chunks
 */
class Wsdl1ToMethodsConverter
{
    public function __invoke(Wsdl1 $wsdl, MethodsConverterContext $context): MethodCollection
    {
        $selectedService = (new Wsdl1SelectedServiceLocator())($wsdl, $context->preferredSoapVersion());

        return new MethodCollection(...filter_nulls(map(
            $selectedService->binding->operations->items,
            fn (BindingOperation $operation): ?Method => $this->parseMethod($selectedService, $operation->name, $context),
        )));
    }

    private function parseMethod(Wsdl1SelectedService $service, string $operationName, MethodsConverterContext $context): ?Method
    {
        $portInfo = $service->portType->operations->lookupByName($operationName);
        if (!$portInfo->isSome()) {
            return null;
        }

        $filterMessageName = static fn (string $namespaced): string => (new QnameParser())($namespaced)[1];
        $lookupMessage = static fn (string $messageName): Option => $service->messages->lookupByName($messageName);

        $inputMessage = OptionsHelper::andThen($portInfo, static fn (Operation $portType) => OptionsHelper::fromNullable($portType->input?->message))
            ->map($filterMessageName);
            // ->andThen($lookupMessage);

        $outputMessage = OptionsHelper::andThen($portInfo, static fn (Operation $portType) => OptionsHelper::fromNullable($portType->output?->message))
            ->map($filterMessageName);
            // ->andThen($lookupMessage);

        return $this->convertOperationMessagesIntoMethod(
            $operationName,
            OptionsHelper::andThen($inputMessage, $lookupMessage),
            OptionsHelper::andThen($outputMessage, $lookupMessage),
            $context
        );
    }

    /**
     * @param Option<Message> $inputMessage
     * @param Option<Message> $outputMessage
     */
    private function convertOperationMessagesIntoMethod(string $operationName, Option $inputMessage, Option $outputMessage, MethodsConverterContext $context): Method
    {
        $filterMessageName = static fn (string $namespaced): string => (new QnameParser())($namespaced)[1];

        // Todo : make sure namespaces match, currently just looking for name is not sufficient!!
        // Todo : what for simple Types in message element? Maybe better to load xsd from wsdl info instead from collected types?
        $convertMessageToTypesDict = static fn(Message $message): array => pull(
            $message->parts->items,
            static fn (Part $part): XsdType => $context->types->fetchFirstByName($filterMessageName($part->element))->getXsdType(),
            static fn (Part $message): string => $message->name
        );

        $parameters = $inputMessage->map($convertMessageToTypesDict)->mapOr(
            static fn (array $types) => map_with_key(
                $types,
                static fn (string $name, XsdType $type) => new Parameter($name, $type)
            ),
            []
        );

        $void = XsdType::guess('void');
        $returnType = $outputMessage->map($convertMessageToTypesDict)->mapOr(
            fn (array $types): XsdType => first($types) ?? $void,
            $void
        );

        return new Method(
            $operationName,
            new ParameterCollection(...$parameters->unwrap()),
            $returnType->unwrap()
        );
    }

    /**
     * TODO - remove me once the above makes sense ;)
     *
     * @return array|Parameter[]

    private function oldyparseXsdTypesFromMessage(SelectedService $service, Message $message): array
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
     * */
}
