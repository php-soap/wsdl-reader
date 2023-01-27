<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

/**
 * There are few options here:
 *
 *      <xs:group ref="wsdl:request-response-or-one-way-operation" />
 *      <xs:group ref="wsdl:solicit-response-or-notification-operation" />
 *
 * Client to server:
 *      Regular Request -> Response
 *      one-way Request -> No response
 *
 * Server to client:
 *     Notification: Output -> no input
 *     Solicit response: Output -> Input
 */
final class Operation
{
    public function __construct(
        public readonly string $name,
        public readonly ?Param $input,
        public readonly ?Param $output,
        public readonly Params $fault,
        public readonly string $documentation,
    ) {
    }

    public function isOneWay(): bool
    {
        return null === $this->output;
    }
}
