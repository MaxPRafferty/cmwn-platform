<?php

namespace Application\Utils;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class AbstractTableFactory
 */
class AbstractTableFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return preg_match("/Table$/", $requestedName);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var Adapter $adapter */
        $adapter   = $container->get(Adapter::class);
        $tableName = str_replace('Table', '', $requestedName);
        $tableName = strtolower($tableName);
        return new TableGateway($tableName, $adapter);
    }
}
