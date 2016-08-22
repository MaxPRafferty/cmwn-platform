<?php

namespace Security\Service;

use User\Service\UserServiceInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SecurityGroupServiceFactory
 */
class SecurityGroupServiceFactory implements FactoryInterface
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
        /** @var Adapter $adapter */
        $adapter = $serviceLocator->get(Adapter::class);

        /** @var UserServiceInterface $userService */
        $userService = $serviceLocator->get(UserServiceInterface::class);

        return new SecurityGroupService(
            new TableGateway('user_groups', $adapter),
            $userService
        );
    }
}
