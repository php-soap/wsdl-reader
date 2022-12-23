<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter;

use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\ParameterCollection;
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Parameter;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Locator\Wsdl1SelectedServiceLocator;
use Soap\WsdlReader\Metadata\Converter\Methods\Converter\MessageToMetadataTypesConverter;
use Soap\WsdlReader\Metadata\Converter\Methods\Detector\OperationMessagesDetector;
use Soap\WsdlReader\Metadata\Converter\Methods\MethodsConverterContext;
use Soap\WsdlReader\Model\Definitions\BindingOperation;
use Soap\WsdlReader\Model\Service\Wsdl1SelectedService;
use Soap\WsdlReader\Model\Wsdl1;
use function Psl\Iter\first;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\map;
use function Psl\Vec\map_with_key;

class Wsdl1ToMethodsConverter
{
    public function __invoke(Wsdl1 $wsdl, MethodsConverterContext $context): MethodCollection
    {
        $selectedService = (new Wsdl1SelectedServiceLocator())($wsdl, $context->preferredSoapVersion);

        return new MethodCollection(...filter_nulls(map(
            $selectedService->binding->operations->items,
            fn (BindingOperation $operation): ?Method => $this->parseMethod($selectedService, $operation->name, $context),
        )));
    }

    private function parseMethod(Wsdl1SelectedService $service, string $operationName, MethodsConverterContext $context): ?Method
    {
        $messages = (new OperationMessagesDetector())($service, $operationName);
        if (!$messages->isSome()) {
            return null;
        }

        ['input' => $inputMessage, 'output' => $outputMessage] = $messages->unwrap();
        $convertMessageToTypesDict = (new MessageToMetadataTypesConverter($context->types))(...);

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
}
