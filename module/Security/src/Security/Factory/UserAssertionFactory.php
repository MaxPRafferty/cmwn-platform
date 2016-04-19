<?php

namespace Security\Factory;

use Security\Authorization\Assertions\UserAssertion;
use Security\Service\SecurityGroupServiceInterface;
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
        /** @var SecurityGroupServiceInterface $securityGroupService */
        $securityGroupService = $serviceLocator->get(SecurityGroupServiceInterface::class);
        return new UserAssertion($securityGroupService);
    }
}
