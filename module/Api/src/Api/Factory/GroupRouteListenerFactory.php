<?php

namespace Api\Factory;

use Api\Listeners\GroupRouteListener;
use Group\Service\GroupServiceInterface;
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
        return new GroupRouteListener($groupService);
    }
}
