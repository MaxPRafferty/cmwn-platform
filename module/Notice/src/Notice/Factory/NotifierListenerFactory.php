<?php

namespace Notice\Factory;

use Notice\NotifierListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class NotifierListenerFactory
 */
class NotifierListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config       = $serviceLocator->get('config');
        $notifyConfig = array_key_exists('notify', $config) ? $config['notify'] : ['listeners' => []];
        return new NotifierListener($serviceLocator, $notifyConfig);
    }
}
