<?php

namespace Group\Service;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GroupServiceFactory
 * @package Group\Service
 * @codeCoverageIgnore
 */
class GroupServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var TableGateway $tableGateway */
        $tableGateway = $serviceLocator->get('GroupsTable');
        return new GroupService($tableGateway);
    }
}
