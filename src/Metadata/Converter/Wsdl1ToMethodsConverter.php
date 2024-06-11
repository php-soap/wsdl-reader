<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter;

use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\ParameterCollection;
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Parameter;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Locator\Wsdl1SelectedServiceLocator;
use Soap\WsdlReader\Metadata\Converter\Methods\Configurator\BindingOperationConfigurator;
use Soap\WsdlReader\Metadata\Converter\Methods\Configurator\PortTypeOperationConfigurator;
use Soap\WsdlReader\Metadata\Converter\Methods\Configurator\Wsdl1SelectedServiceConfigurator;
use Soap\WsdlReader\Metadata\Converter\Methods\Converter\MessageToMetadataTypesConverter;
use Soap\WsdlReader\Metadata\Converter\Methods\Detector\OperationMessagesDetector;
use Soap\WsdlReader\Metadata\Converter\Methods\MethodsConverterContext;
use Soap\WsdlReader\Model\Definitions\BindingOperation;
use Soap\WsdlReader\Model\Service\Wsdl1SelectedService;
use Soap\WsdlReader\Model\Wsdl1;
use function Psl\Fun\pipe;
use function Psl\Iter\first;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\map;
use function Psl\Vec\map_with_key;

final class Wsdl1ToMethodsConverter
{
    public function __invoke(Wsdl1 $wsdl, MethodsConverterContext $context): MethodCollection
    {
        $selectedService = (new Wsdl1SelectedServiceLocator())($wsdl, $context->serviceCriteria);

        return new MethodCollection(...filter_nulls(map(
            $selectedService->binding->operations->items,
            fn (BindingOperation $operation): ?Method => $this->parseMethod($selectedService, $operation, $context),
        )));
    }

    private function parseMethod(Wsdl1SelectedService $service, BindingOperation $bindingOperation, MethodsConverterContext $context): ?Method
    {
        $operationName = $bindingOperation->name;
        $portTypeOperation = $service->portType->operations->lookupByName($operationName)->unwrapOr(null);
        if (!$portTypeOperation) {
            return null;
        }

        ['input' => $inputMessage, 'output' => $outputMessage] = (new OperationMessagesDetector())($service, $portTypeOperation);
        $convertMessageToTypesDict = (new MessageToMetadataTypesConverter($context->types, $service->namespaces))(...);

        $parameters = $inputMessage->map($convertMessageToTypesDict)->mapOr(
            static fn (array $types) => map_with_key(
                $types,
                static fn (string $name, XsdType $type) => new Parameter($name, $type)
            ),
            []
        );

        $returnType = $outputMessage->map($convertMessageToTypesDict)->mapOr(
            static fn (array $types): XsdType => match (count($types)) {
                0 => XsdType::void(),
                1 => first($types),
                default => XsdType::guess('array')
            },
            XsdType::void()
        );

        $configure = pipe(
            static fn (Method $method) => (new Wsdl1SelectedServiceConfigurator())($method, $service),
            static fn (Method $method) => (new BindingOperationConfigurator())($method, $bindingOperation),
            static fn (Method $method) => (new PortTypeOperationConfigurator())($method, $portTypeOperation),
        );

        return $configure(
            new Method(
                $operationName,
                new ParameterCollection(...$parameters->unwrap()),
                $returnType->unwrap()
            )
        );
    }
}
