<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Detector;

use Psl\Option\Option;
use Soap\WsdlReader\Model\Definitions\Message;
use Soap\WsdlReader\Model\Service\Wsdl1SelectedService;
use Soap\WsdlReader\Parser\Definitions\QNamed;
use Soap\WsdlReader\Todo\OptionsHelper;
use function Psl\Option\none;
use function Psl\Option\some;

class OperationMessagesDetector
{
    /**
     * @return Option<array{input: Option<Message>, output: Option<Message>}>
     */
    public function __invoke(Wsdl1SelectedService $service, string $operationName): Option
    {
        $portType = $service->portType->operations->lookupByName($operationName)->unwrapOr(null);
        if (!$portType) {
            return none();
        }

        $lookupMessage = static fn (QNamed $message): Option => $service->messages->lookupByName($message->localName);

        $inputMessage = OptionsHelper::andThen(OptionsHelper::fromNullable($portType->input?->message), $lookupMessage);
        $outputMessage = OptionsHelper::andThen(OptionsHelper::fromNullable($portType->output?->message), $lookupMessage);

        return some([
            'input' => $inputMessage,
            'output' => $outputMessage,
        ]);
    }
}
