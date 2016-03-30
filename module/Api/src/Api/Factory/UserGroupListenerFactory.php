<?php

namespace Api\Factory;

use Api\Listeners\UserGroupListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserGroupListenerFactory
 *
 * ${CARET}
 */
class UserGroupListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Group\Service\UserGroupServiceInterface $userGroupService */
        $userGroupService = $serviceLocator->get('Group\Service\UserGroupServiceInterface');
        return new UserGroupListener($userGroupService);
    }
}
