<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Locator;

use Psl\Option\Option;
use Soap\WsdlReader\Exception\ServiceException;
use Soap\WsdlReader\Model\Definitions\Binding;
use Soap\WsdlReader\Model\Definitions\Port;
use Soap\WsdlReader\Model\Service\Wsdl1SelectedService;
use Soap\WsdlReader\Model\Wsdl1;

/**
 * Searches best fit for services -> ports -> bindings
 */
final class Wsdl1SelectedServiceLocator
{
    public function __invoke(Wsdl1 $wsdl, ServiceSelectionCriteria $criteria): Wsdl1SelectedService
    {
        foreach ($wsdl->services->items as $service) {
            $port = $service->ports->lookupByLookupServiceCriteria($criteria);
            $binding = $port->andThen(
                static fn (Port $port): Option => $wsdl->bindings->lookupByName($port->binding->localName)
            );
            $portType = $binding->andThen(
                static fn (Binding $binding): Option => $wsdl->portTypes->lookupByName($binding->type->localName)
            );

            if ($portType->isSome()) {
                return new Wsdl1SelectedService(
                    wsdl: $wsdl,
                    service: $service,
                    port: $port->unwrap(),
                    binding: $binding->unwrap(),
                    portType: $portType->unwrap(),
                    messages: $wsdl->messages,
                    namespaces: $wsdl->namespaces,
                );
            }
        }

        throw ServiceException::notFound($criteria->preferredSoapVersion);
    }
}
