<?php

namespace Api\Factory;

use Api\Listeners\UserGroupListener;
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
        /** @var \Group\Service\UserGroupServiceInterface $userGroupService */
        $userGroupService = $serviceLocator->get('Group\Service\UserGroupServiceInterface');

        /** @var OrganizationServiceInterface $orgService */
        $orgService    = $serviceLocator->get('Org\Service\OrganizationServiceInterface');
        return new UserGroupListener($userGroupService, $orgService);
    }
}
