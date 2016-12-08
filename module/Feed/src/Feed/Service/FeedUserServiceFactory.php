<?php

namespace Feed\Service;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FeedUserServiceFactory
 * @package Feed\Service
 */
class FeedUserServiceFactory implements FactoryInterface
{
    /**@inheritdoc*/
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = $serviceLocator->get(Adapter::class);

        return new FeedService(new TableGateway('user_feed', $adapter));
    }
}
