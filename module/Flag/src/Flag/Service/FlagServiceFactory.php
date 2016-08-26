<?php

namespace Flag\Service;

use Flag\FlagHydrator;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FlagServiceFactory
 * @package Flag\Service
 */
class FlagServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FlagService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**@var FlagHydrator $hydrator*/
        $hydrator = $serviceLocator->get(FlagHydrator::class);
        $adapter = $serviceLocator->get(Adapter::class);

        return new FlagService(new TableGateway('image_flags', $adapter), $hydrator);
    }
}
