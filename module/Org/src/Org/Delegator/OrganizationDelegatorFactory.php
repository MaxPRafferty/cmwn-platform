<?php

namespace Org\Delegator;

use Org\Service\OrganizationService;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Creates a organization Delegator
 *
 * @package Organization\Delegator
 * @codeCoverageIgnore
 */
class OrganizationDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * A factory that creates delegates of a given service
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string $name the normalized service name
     * @param string $requestedName the requested service name
     * @param callable $callback the callback that is responsible for creating the service
     *
     * @return mixed
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /** @var OrganizationService $orgService */
        $orgService = call_user_func($callback);
        $delegator   = new OrganizationServiceDelegator($orgService);
        return $delegator;
    }

}
