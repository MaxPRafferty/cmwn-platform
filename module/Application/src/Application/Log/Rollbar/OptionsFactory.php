<?php

namespace Application\Log\Rollbar;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class OptionsFactory
 */
class OptionsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Options
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config   = $serviceLocator->get('Config');
        $settings = isset($config['log-settings']) ? $config['log-settings'] : [];
        $rollBar  = isset($settings['rollbar']) ? $settings['rollbar'] : [];

        return new Options($rollBar);
    }
}
