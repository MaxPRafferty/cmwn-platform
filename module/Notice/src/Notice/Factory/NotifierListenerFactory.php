<?php

namespace Notice\Factory;

use Interop\Container\ContainerInterface;
use Notice\NotifierListener;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class NotifierListenerFactory
 */
class NotifierListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config       = $container->get('config');
        $notifyConfig = array_key_exists('notify', $config) ? $config['notify'] : ['listeners' => []];
        return new NotifierListener($container, $notifyConfig);
    }
}
