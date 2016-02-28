<?php

namespace Application\Utils;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractTableFactory implements AbstractFactoryInterface
{
    public function canCreateServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        return preg_match("/Table$/", $requestedName);
    }

    public function createServiceWithName(ServiceLocatorInterface $services, $name, $requestedName)
    {
        $adapter   = $services->get('Zend\Db\Adapter\Adapter');
        $tableName = str_replace('Table', '', $requestedName);
        $tableName = strtolower($tableName);
        return new TableGateway($tableName, $adapter);
    }
}