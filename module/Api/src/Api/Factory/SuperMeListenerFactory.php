<?php

namespace Api\Factory;

use Api\Listeners\SuperMeListener;
use Group\Service\GroupServiceInterface;
use Org\Service\OrganizationServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SuperMeListenerFactory
 */
class SuperMeListenerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SuperMeListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var GroupServiceInterface $groupService */
        /** @var OrganizationServiceInterface $orgService */
        $groupService = $serviceLocator->get(GroupServiceInterface::class);
        $orgService   = $serviceLocator->get(OrganizationServiceInterface::class);

        return new SuperMeListener($orgService, $groupService);
    }
}
