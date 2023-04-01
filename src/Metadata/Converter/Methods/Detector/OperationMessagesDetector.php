<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Detector;

use Psl\Option\Option;
use Soap\WsdlReader\Model\Definitions\Message;
use Soap\WsdlReader\Model\Definitions\Operation;
use Soap\WsdlReader\Model\Definitions\QNamed;
use Soap\WsdlReader\Model\Service\Wsdl1SelectedService;
use function Psl\Option\from_nullable;

final class OperationMessagesDetector
{
    /**
     * @return array{input: Option<Message>, output: Option<Message>}
     */
    public function __invoke(Wsdl1SelectedService $service, Operation $operation): array
    {
        $lookupMessage = static fn (QNamed $message): Option => $service->messages->lookupByName($message->localName);

        $inputMessage = from_nullable($operation->input?->message)->andThen($lookupMessage);
        $outputMessage = from_nullable($operation->output?->message)->andThen($lookupMessage);

        return [
            'input' => $inputMessage,
            'output' => $outputMessage,
        ];
    }
}
