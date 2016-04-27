<?php

namespace Application\Utils;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractTableFactory
 *
 * Creates a default table gateway based off the requested name
 */
class AbstractTableFactory implements AbstractFactoryInterface
{
    /**
     * @param ServiceLocatorInterface $services
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        return preg_match("/Table$/", $requestedName);
    }

    /**
     * @param ServiceLocatorInterface $services
     * @param $name
     * @param $requestedName
     * @return TableGateway
     */
    public function createServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        /** @var Adapter $adapter */
        $adapter   = $services->get(Adapter::class);
        $tableName = str_replace('Table', '', $requestedName);
        $tableName = strtolower($tableName);
        return new TableGateway($tableName, $adapter);
    }
}
