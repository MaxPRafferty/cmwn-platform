<?php

namespace Security\Factory;

use Group\Service\UserGroupServiceInterface;
use Security\Listeners\GroupServiceListener;
use Security\Service\SecurityOrgServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GroupServiceListenerFactory
 */
class GroupServiceListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var UserGroupServiceInterface $userGroupService */
        $userGroupService = $serviceLocator->get(UserGroupServiceInterface::class);
        /** @var SecurityOrgServiceInterface $securityOrgService */
        $securityOrgService = $serviceLocator->get(SecurityOrgServiceInterface::class);
        return new GroupServiceListener($userGroupService, $securityOrgService);
    }
}
