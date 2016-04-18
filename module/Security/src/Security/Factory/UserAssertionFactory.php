<?php

namespace Security\Factory;

use Group\Service\UserGroupServiceInterface;
use Security\Authorization\Assertions\UserAssertion;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserAssertionFactory
 */
class UserAssertionFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var UserGroupServiceInterface $userGroupService */
        $userGroupService = $serviceLocator->get(UserGroupServiceInterface::class);
        return new UserAssertion($userGroupService);
    }
}
