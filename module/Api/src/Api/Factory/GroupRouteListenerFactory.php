<?php

namespace Api\Factory;

use Api\Listeners\GroupRouteListener;
use Group\Service\GroupServiceInterface;
use Security\Service\SecurityOrgService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GroupRouteListenerFactory
 *
 * ${CARET}
 */
class GroupRouteListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var GroupServiceInterface $groupService */
        $groupService = $serviceLocator->get(GroupServiceInterface::class);
        $orgService = $serviceLocator->get(SecurityOrgService::class);
        return new GroupRouteListener($groupService, $orgService);
    }
}
