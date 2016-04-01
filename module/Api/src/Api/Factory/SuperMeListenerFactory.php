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
        $groupService = $serviceLocator->get('Group\Service');

        /** @var OrganizationServiceInterface $orgService */
        $orgService   = $serviceLocator->get('Organization\Service');

        return new SuperMeListener($orgService, $groupService);
    }
}
