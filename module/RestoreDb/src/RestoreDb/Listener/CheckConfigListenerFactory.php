<?php

namespace RestoreDb\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CheckConfigListenerFactory
 * @package RestoreDb\Listener
 */
class CheckConfigListenerFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        return new CheckConfigListener(isset($config['allow-reset']) && $config['allow-reset']);
    }
}
