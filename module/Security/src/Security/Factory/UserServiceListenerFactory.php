<?php

namespace Security\Factory;

use Group\Service\UserGroupServiceInterface;
use Security\Listeners\UserServiceListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserServiceListenerFactory
 */
class UserServiceListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var UserGroupServiceInterface $userGroupService */
        $userGroupService = $serviceLocator->get(UserGroupServiceInterface::class);
        return new UserServiceListener($userGroupService);
    }
}
