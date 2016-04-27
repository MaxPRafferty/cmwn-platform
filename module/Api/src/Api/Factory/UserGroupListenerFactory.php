<?php

namespace Api\Factory;

use Api\Listeners\UserGroupListener;
use Group\Service\UserGroupServiceInterface;
use Org\Service\OrganizationServiceInterface;
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
        /** @var UserGroupServiceInterface $userGroupService */
        /** @var OrganizationServiceInterface $orgService */
        $userGroupService = $serviceLocator->get(UserGroupServiceInterface::class);
        $orgService       = $serviceLocator->get(OrganizationServiceInterface::class);

        return new UserGroupListener($userGroupService, $orgService);
    }
}
