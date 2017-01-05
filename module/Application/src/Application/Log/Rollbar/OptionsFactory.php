<?php

namespace Application\Log\Rollbar;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class OptionsFactory
 */
class OptionsFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config   = $container->get('Config');
        $settings = isset($config['log-settings']) ? $config['log-settings'] : [];
        $rollBar  = isset($settings['rollbar']) ? $settings['rollbar'] : [];

        return new Options($rollBar);
    }
}
