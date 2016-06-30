<?php

namespace Friend\Service;

use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SuggestedFriendServiceFactory
 */
class SuggestedFriendServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Adapter $adapter */
        $adapter   = $serviceLocator->get(Adapter::class);
        return new SuggestedFriendService($adapter);
    }
}
