<?php

namespace Notice;

use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManager;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 * @codeCoverageIgnore
 */
class Module implements ConfigProviderInterface, AutoloaderProviderInterface, BootstrapListenerInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $event
     * @return array
     */
    public function onBootstrap(EventInterface $event)
    {
        if (defined('TEST_MODE') && TEST_MODE == true) {
            return;
        }

        /** @var MvcEvent $event */
        /** @var \Notice\NotifierListener $listener */
        $listener = $event->getApplication()->getServiceManager()->get('Notice\NotifierListener');
        /** @var SharedEventManager $sharedEvents */
        $sharedEvents = $event->getApplication()->getServiceManager()->get('SharedEventManager');
        $listener->attachShared($sharedEvents);
    }
}
