<?php

namespace Group\Service;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserGroupSerivceFactory
 * @package Group\Service
 */
class UserGroupServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Zend\Db\Adapter\Adapter $adapter */
        $adapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
        return new UserGroupService(
            new TableGateway('users_group', $adapter)
        );
    }
}
