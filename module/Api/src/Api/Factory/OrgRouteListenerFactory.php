<?php

namespace Api\Factory;

use Api\Listeners\OrgRouteListener;
use Org\Service\OrganizationServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class OrgRouteListenerFactory
 */
class OrgRouteListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Org\Service\OrganizationServiceInterface $orgService */
        $orgService = $serviceLocator->get(OrganizationServiceInterface::class);
        return new OrgRouteListener($orgService);
    }
}
