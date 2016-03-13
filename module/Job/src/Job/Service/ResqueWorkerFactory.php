<?php

namespace Job\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ResqueJobFactory
 */
class ResqueWorkerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $queue  = getenv('QUEUE');
        $queues = explode(',', $queue);
        return new ResqueWorker($queues, $serviceLocator);
    }
}
